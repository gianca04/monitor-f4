<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\District;
use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PeruLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $excelPath = base_path('peru.xlsx');
        $htmlPath = base_path('peru.html');

        $rowsData = [];

        if (file_exists($excelPath)) {
            $this->command?->info("Cargando datos desde: {$excelPath}");
            $spreadsheet = IOFactory::load($excelPath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            for ($row = 4; $row <= $highestRow; $row++) {
                $num = trim((string) $sheet->getCell("A{$row}")->getValue());
                $districtName = trim((string) $sheet->getCell("B{$row}")->getValue());
                $provinceName = trim((string) $sheet->getCell("C{$row}")->getValue());
                $departmentName = trim((string) $sheet->getCell("D{$row}")->getValue());

                if (is_numeric($num) && !empty($districtName) && !empty($provinceName) && !empty($departmentName)) {
                    $rowsData[] = [
                        'district' => preg_replace('/\s+/', ' ', $districtName),
                        'province' => preg_replace('/\s+/', ' ', $provinceName),
                        'department' => preg_replace('/\s+/', ' ', $departmentName),
                    ];
                }
            }
        } elseif (file_exists($htmlPath)) {
            $this->command?->info("Cargando datos desde HTML: {$htmlPath}");
            $html = file_get_contents($htmlPath);

            preg_match_all('/<TR[^>]*>\s*<TD[^>]*>\s*<FONT[^>]*>\s*(\d+)\s*<\/FONT>\s*<\/TD>\s*<TD[^>]*>\s*<FONT[^>]*>\s*(.*?)\s*<\/FONT>\s*<\/TD>\s*<TD[^>]*>\s*<FONT[^>]*>\s*(.*?)\s*<\/FONT>\s*<\/TD>\s*<TD[^>]*>\s*<FONT[^>]*>\s*(.*?)\s*<\/FONT>\s*<\/TD>\s*<\/TR>/is', $html, $matches, PREG_SET_ORDER);

            foreach ($matches as $m) {
                $rowsData[] = [
                    'district' => preg_replace('/\s+/', ' ', trim(html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'))),
                    'province' => preg_replace('/\s+/', ' ', trim(html_entity_decode($m[3], ENT_QUOTES | ENT_HTML5, 'UTF-8'))),
                    'department' => preg_replace('/\s+/', ' ', trim(html_entity_decode($m[4], ENT_QUOTES | ENT_HTML5, 'UTF-8'))),
                ];
            }
        } else {
            $this->command?->error("No se encontró ni peru.xlsx ni peru.html en la raíz del proyecto.");
            return;
        }

        $this->command?->info("Procesando " . count($rowsData) . " registros...");

        // Pre-cargar registros existentes para validar in-memory de forma ultrarrápida
        $departmentsMap = Department::all()->pluck('id', 'name')->toArray();
        $provincesMap = []; // Key: "department_id:province_name" => id
        foreach (Province::all() as $p) {
            $provincesMap["{$p->department_id}:{$p->name}"] = $p->id;
        }

        $districtsMap = []; // Key: "province_id:district_name" => id
        foreach (District::all() as $d) {
            $districtsMap["{$d->province_id}:{$d->name}"] = $d->id;
        }

        DB::beginTransaction();
        try {
            $createdDepts = 0;
            $createdProvs = 0;
            $createdDists = 0;

            foreach ($rowsData as $data) {
                $deptName = mb_strtoupper($data['department'], 'UTF-8');
                $provName = mb_strtoupper($data['province'], 'UTF-8');
                $distName = mb_strtoupper($data['district'], 'UTF-8');

                // 1. Validar / Crear Departamento
                if (!isset($departmentsMap[$deptName])) {
                    $department = Department::firstOrCreate(['name' => $deptName]);
                    $departmentsMap[$deptName] = $department->id;
                    $createdDepts++;
                }
                $departmentId = $departmentsMap[$deptName];

                // 2. Validar / Crear Provincia bajo ese Departamento
                $provKey = "{$departmentId}:{$provName}";
                if (!isset($provincesMap[$provKey])) {
                    $province = Province::firstOrCreate([
                        'department_id' => $departmentId,
                        'name' => $provName,
                    ]);
                    $provincesMap[$provKey] = $province->id;
                    $createdProvs++;
                }
                $provinceId = $provincesMap[$provKey];

                // 3. Validar / Crear Distrito bajo esa Provincia
                $distKey = "{$provinceId}:{$distName}";
                if (!isset($districtsMap[$distKey])) {
                    $district = District::firstOrCreate([
                        'province_id' => $provinceId,
                        'name' => $distName,
                    ]);
                    $districtsMap[$distKey] = $district->id;
                    $createdDists++;
                }
            }

            DB::commit();

            $this->command?->info("Siembra completada exitosamente.");
            $this->command?->info("Departamentos (nuevos/verificados): " . count($departmentsMap) . " ({$createdDepts} creados)");
            $this->command?->info("Provincias (nuevas/verificadas): " . count($provincesMap) . " ({$createdProvs} creadas)");
            $this->command?->info("Distritos (nuevos/verificados): " . count($districtsMap) . " ({$createdDists} creados)");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command?->error("Error durante el sembrado: " . $e->getMessage());
            throw $e;
        }
    }
}
