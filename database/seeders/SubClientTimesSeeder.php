<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\District;
use App\Models\Province;
use App\Models\SubClient;
use Illuminate\Database\Seeder;

class SubClientTimesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
            [
                'subclient_name' => 'NVO CHIMBOTE - PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'SANTA',
                'district_name' => 'NUEVO CHIMBOTE',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25102049',
                'address' => 'KM. 424 PAMPAS DE CHIMBOTE (COLINDANTE CON EL AEROPUERTO) ANCAS SANTA - NUEVO CHIMBOTE',
            ],
            [
                'subclient_name' => 'SUPERMERCADOS SKA - PVE',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'SANTA',
                'district_name' => 'CHIMBOTE',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25101006',
                'address' => 'JR MANUEL VILLAVICENCIO 476 ANCASH SANTA - CHIMBOTE',
            ],
            [
                'subclient_name' => 'PIURA - PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'PIURA',
                'district_name' => 'PIURA',
                'arrival_time_hrs' => 2,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => null,
                'address' => null,
            ],
            [
                'subclient_name' => 'PAITA - PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'PAITA',
                'district_name' => 'PAITA',
                'arrival_time_hrs' => 4,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25102075',
                'address' => 'AV. ALMIRANTE MIGUEL GRAU 514 (SUB LOTE B,C,D,E,F,G) PIURA - PAITA',
            ],
            [
                'subclient_name' => 'CHICLAYO - MK',
                'client_business_name' => 'MAKRO SUPERMAYORISTA S.A.',
                'province_name' => 'CHICLAYO',
                'district_name' => 'CHICLAYO',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '24107030',
                'address' => 'AV. JORGE BASADRE #299 URB. CAMPODÓNICO (ANTES AV. MIGUEL GRAU)',
            ],
            [
                'subclient_name' => 'TALARA - PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'TALARA',
                'district_name' => 'PARIÑAS',
                'arrival_time_hrs' => 4,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25102018',
                'address' => 'AV. MIGUEL DE CERVANTES 300 INT. LT. 6 C.C. REAL PLAZA',
            ],
            [
                'subclient_name' => 'TUMBES - PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'TUMBES',
                'district_name' => 'TUMBES',
                'arrival_time_hrs' => 8,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25102106',
                'address' => 'AV. TUMBES S/N INTERS. CALLE LA MARINA',
            ],
            [
                'subclient_name' => 'CHICLAYO - PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'CHICLAYO',
                'district_name' => 'CHICLAYO',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '24107030',
                'address' => 'AV. JORGE BASADRE #299 URB. CAMPODÓNICO (ANTES AV. MIGUEL GRAU)',
            ],
            [
                'subclient_name' => 'TALARA MUNICIPALIDAD - PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'TALARA',
                'district_name' => 'PARIÑAS',
                'arrival_time_hrs' => 4,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25102018',
                'address' => 'AV. MIGUEL DE CERVANTES 300 INT. LT. 6 C.C. REAL PLAZA',
            ],
            [
                'subclient_name' => 'TRUJILLO MANSICHE - PVS',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'TRUJILLO',
                'district_name' => 'TRUJILLO',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25102122',
                'address' => 'AV. PANAMERICANA 639',
            ],
            [
                'subclient_name' => 'PIURA - MK',
                'client_business_name' => 'MAKRO SUPERMAYORISTA S.A.',
                'province_name' => 'PIURA',
                'district_name' => 'PIURA',
                'arrival_time_hrs' => 2,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '24107033',
                'address' => 'PROLONGACIÓN AV. SANCHEZ CERRO SUB LOTE B-1 DISTRITO 27 DE OCTUBRE.',
            ],
            [
                'subclient_name' => 'EL CHACARERO - PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'TRUJILLO',
                'district_name' => 'TRUJILLO',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25102028',
                'address' => 'URB. LOS PORTALES MZ Q LT 1 TRUJILLO',
            ],
            [
                'subclient_name' => 'TRUJILLO - PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'TRUJILLO',
                'district_name' => 'TRUJILLO',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25102017',
                'address' => 'FUNDO LAS CASUARINAS SUB LOTE PREDIO A LOTE 5',
            ],
            [
                'subclient_name' => 'TRUJILLO VALCARCEL - PVS',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'TRUJILLO',
                'district_name' => 'TRUJILLO',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25103045',
                'address' => 'AV. TEODORO VALCARCEL 266 N° 268 URB. PRIMAVERA - LA LIBERTAD TRUJILLO',
            ],
            [
                'subclient_name' => 'CHIMBOTE - PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'SANTA',
                'district_name' => 'CHIMBOTE',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25102032',
                'address' => 'JR. FRANCISCO BOLOGNESI S/N (ESQ. JR. TUMBES) ANCASH SANTA - CHIMBOTE',
            ],
            [
                'subclient_name' => 'CHIMBOTE - MK',
                'client_business_name' => 'MAKRO SUPERMAYORISTA S.A.',
                'province_name' => 'SANTA',
                'district_name' => 'CHIMBOTE',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '24107041',
                'address' => 'ZONA LOTIZACION INDUSTRIAL GRAN TRAPECIO, PARCELA 3 MZ C - LOTES 4,5,6,7,8',
            ],
            [
                'subclient_name' => 'SULLANA - PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'SULLANA',
                'district_name' => 'SULLANA',
                'arrival_time_hrs' => 4,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => null,
                'address' => null,
            ],
            [
                'subclient_name' => 'CAJAMARCA - PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'CAJAMARCA',
                'district_name' => 'CAJAMARCA',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25102056',
                'address' => 'AV. EVITAMIENTO NORTE LOTE 1. CAJAMARCA',
            ],
            [
                'subclient_name' => 'TRUJILLO - MK',
                'client_business_name' => 'MAKRO SUPERMAYORISTA S.A.',
                'province_name' => 'TRUJILLO',
                'district_name' => 'TRUJILLO',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '24107032',
                'address' => 'ESQUINA AV. NICOLÁS DE PIÉROLA, AV. MICAELA BATISDAS CON, TRUJILLO',
            ],
            [
                'subclient_name' => 'CHICLAYO AVENTURA - PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'CHICLAYO',
                'district_name' => 'CHICLAYO',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '24107030',
                'address' => 'AV. JORGE BASADRE #299 URB. CAMPODÓNICO (ANTES AV. MIGUEL GRAU)',
            ],
            [
                'subclient_name' => 'SULLANA - MK',
                'client_business_name' => 'MAKRO SUPERMAYORISTA S.A.',
                'province_name' => 'SULLANA',
                'district_name' => 'SULLANA',
                'arrival_time_hrs' => 4,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25102018',
                'address' => 'AV. MIGUEL DE CERVANTES 300 INT. LT. 6 C.C. REAL PLAZA',
            ],
            [
                'subclient_name' => 'TRUJILLO 2 - MK',
                'client_business_name' => 'MAKRO SUPERMAYORISTA S.A.',
                'province_name' => 'TRUJILLO',
                'district_name' => 'TRUJILLO',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25102122',
                'address' => 'AV. PANAMERICANA 639',
            ],
            [
                'subclient_name' => 'PIURA 2 - MK',
                'client_business_name' => 'MAKRO SUPERMAYORISTA S.A.',
                'province_name' => 'PIURA',
                'district_name' => 'PIURA',
                'arrival_time_hrs' => 2,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '24107006',
                'address' => 'AV. SANCHEZ CERRO S/N, ZONA INDUSTRIAL II MZ Z LT 1-5-A, VEINTISÉIS DE OCTUBRE',
            ],
            [
                'subclient_name' => 'CENTRO TRUJILLO PVH',
                'client_business_name' => 'COMPAÑÍA FOOD RETAIL S.A.C',
                'province_name' => 'TRUJILLO',
                'district_name' => 'TRUJILLO',
                'arrival_time_hrs' => 24,
                'corrective_quote_time_hrs' => 72,
                'corrective_execution_time_hrs' => 120,
                'ceco' => '25101005',
                'address' => 'AV. ESPAÑA 2420 LA LIBERTAD TRUJILLO',
            ],
        ];

        $updatedCount = 0;
        $createdCount = 0;

        foreach ($stores as $store) {
            // 1. Buscar o crear el Cliente
            $clientName = trim($store['client_business_name']);
            $client = Client::where('business_name', 'LIKE', '%' . $clientName . '%')->first();

            if (!$client) {
                $client = Client::create([
                    'business_name' => $clientName,
                    'person_type' => 'JURIDICA',
                    'document_type' => 'RUC',
                    'document_number' => '20000000000',
                ]);
            }

            // 2. Resolver el distrito y la provincia exacta en el contexto geográfico peruano
            $districtId = null;
            if (!empty($store['province_name'])) {
                $province = Province::where('name', trim($store['province_name']))->first();
                if (!$province) {
                    $province = Province::where('name', 'LIKE', '%' . trim($store['province_name']) . '%')->first();
                }

                if ($province) {
                    $districtQuery = District::where('province_id', $province->id);
                    $district = null;
                    if (!empty($store['district_name'])) {
                        $district = (clone $districtQuery)->where('name', 'LIKE', '%' . trim($store['district_name']) . '%')->first();
                    }
                    if (!$district) {
                        $district = $districtQuery->first();
                    }
                    if ($district) {
                        $districtId = $district->id;
                    }
                }
            }

            // 3. Buscar si el subcliente ya existe por nombre y cliente
            $subClient = SubClient::where('name', trim($store['subclient_name']))
                ->where('client_id', $client->id)
                ->first();

            $updateData = [
                'arrival_time_hrs' => $store['arrival_time_hrs'],
                'corrective_quote_time_hrs' => $store['corrective_quote_time_hrs'],
                'corrective_execution_time_hrs' => $store['corrective_execution_time_hrs'],
            ];

            if ($districtId) {
                $updateData['district_id'] = $districtId;
            }

            if (!is_null($store['ceco'])) {
                $updateData['ceco'] = $store['ceco'];
            }

            if (!is_null($store['address'])) {
                $updateData['address'] = $store['address'];
            }

            if ($subClient) {
                $subClient->update($updateData);
                $updatedCount++;
                if ($this->command) {
                    $distName = $districtId ? District::with('province')->find($districtId)?->name : 'Sin distrito';
                    $this->command->info("Actualizado: {$subClient->name} [Dist: {$distName}] ({$client->business_name})");
                }
            } else {
                $createData = array_merge([
                    'name' => trim($store['subclient_name']),
                    'client_id' => $client->id,
                    'district_id' => $districtId,
                ], $updateData);

                $newSubClient = SubClient::create($createData);
                $createdCount++;
                if ($this->command) {
                    $distName = $districtId ? District::with('province')->find($districtId)?->name : 'Sin distrito';
                    $this->command->info("Creado nuevo: {$newSubClient->name} [Dist: {$distName}] ({$client->business_name})");
                }
            }
        }

        if ($this->command) {
            $this->command->info("Seeder completado. Registros actualizados: {$updatedCount}. Registros creados: {$createdCount}.");
        }
    }
}
