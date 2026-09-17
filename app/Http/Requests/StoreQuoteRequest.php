<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\QuoteItemType;

/**
 * Form Request para validar la creación de una cotización.
 */
class StoreQuoteRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Ajustar según la lógica de autorización necesaria
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => 'nullable|exists:projects,id',
            'request_number' => 'nullable|string|max:255',
            'employee_id' => 'nullable|exists:employees,id',
            'sub_client_id' => 'required|exists:sub_clients,id',
            'quote_category_id' => 'required|exists:quote_categories,id',
            'energy_sci_manager' => 'nullable|string|max:255',
            'ceco' => 'nullable|string|max:255',
            'project_name' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'sub_client_name' => 'nullable|string|max:255',
            'quote_date' => 'nullable|date',
            'execution_date' => 'nullable|date',
            'status' => 'nullable|string|in:Pendiente,Enviado,Aprobado,Anulado',
            'quote_type' => ['nullable', Rule::enum(\App\Enums\QuoteType::class)],

            // Validación para grupos y sus items (usado por QuoteManager / interfaz reactiva)
            'groups' => 'nullable|array',
            'groups.*.name' => 'nullable|string|max:255',
            'groups.*.items' => 'nullable|array',
            'groups.*.items.*.pricelist_id' => 'required|exists:pricelists,id',
            'groups.*.items.*.budget_code' => 'nullable|string|max:50',
            'groups.*.items.*.quantity' => 'required|numeric|min:0.01',
            'groups.*.items.*.unit_price' => 'required|numeric|min:0',
            'groups.*.items.*.item_type' => ['required', Rule::enum(QuoteItemType::class)],
            'groups.*.items.*.comment' => 'nullable|string',

            // Validación para items directos (formato plano)
            'items' => 'nullable|array',
            'items.*.pricelist_id' => 'required|exists:pricelists,id',
            'items.*.budget_code' => 'nullable|string|max:50',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.item_type' => ['required', Rule::enum(QuoteItemType::class)],
            'items.*.comment' => 'nullable|string',
        ];
    }

    /**
     * Configurar el validador para verificar que exista al menos una partida.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasItems = false;

            if ($this->has('groups') && is_array($this->input('groups'))) {
                foreach ($this->input('groups') as $group) {
                    if (!empty($group['items']) && is_array($group['items']) && count($group['items']) > 0) {
                        $hasItems = true;
                        break;
                    }
                }
            } elseif ($this->has('items') && is_array($this->input('items')) && count($this->input('items')) > 0) {
                $hasItems = true;
            }

            if (!$hasItems) {
                $validator->errors()->add('items', 'Debe agregar al menos una partida o ítem a la cotización.');
            }
        });
    }

    /**
     * Nombres legibles de los atributos para los mensajes de validación.
     */
    public function attributes(): array
    {
        return [
            'project_name' => 'nombre del servicio / proyecto',
            'sub_client_id' => 'cliente / tienda',
            'quote_category_id' => 'categoría',
            'request_number' => 'número de solicitud',
            'employee_id' => 'empleado',
            'energy_sci_manager' => 'gerente energy sci',
            'ceco' => 'CECO',
            'quote_date' => 'fecha de cotización',
            'execution_date' => 'fecha de ejecución',
            'status' => 'estado',
            'quote_type' => 'tipo de cotización',
            'groups' => 'grupos',
            'items' => 'partidas / ítems',
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados para las reglas de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'project_name.required' => 'El nombre del servicio o proyecto es obligatorio.',
            'project_name.string' => 'El nombre del servicio o proyecto debe ser texto.',
            'project_name.max' => 'El nombre del servicio o proyecto no puede exceder 255 caracteres.',
            'service_name.required' => 'El nombre del servicio es obligatorio.',
            'service_name.max' => 'El nombre del servicio no puede exceder 255 caracteres.',

            'sub_client_id.required' => 'Debe seleccionar un cliente / tienda para el proyecto y cotización.',
            'sub_client_id.exists' => 'El cliente / tienda seleccionado no existe.',

            'quote_category_id.required' => 'Debe seleccionar una categoría para la cotización.',
            'quote_category_id.exists' => 'La categoría seleccionada no existe.',

            'request_number.max' => 'El número de solicitud no puede exceder 255 caracteres.',
            'employee_id.exists' => 'El empleado seleccionado no existe.',
            'energy_sci_manager.max' => 'El nombre del gerente no puede exceder 255 caracteres.',
            'ceco.max' => 'El CECO no puede exceder 255 caracteres.',
            'client_name.max' => 'El nombre del cliente no puede exceder 255 caracteres.',
            'sub_client_name.max' => 'El nombre del subcliente no puede exceder 255 caracteres.',
            'quote_date.date' => 'La fecha de cotización no es válida.',
            'execution_date.date' => 'La fecha de ejecución no es válida.',
            'status.in' => 'El estado debe ser uno de: Pendiente, Enviado, Aprobado, Anulado.',

            'groups.array' => 'El formato de grupos no es válido.',
            'groups.*.items.*.pricelist_id.required' => 'Cada ítem en los grupos debe tener un elemento del preciario.',
            'groups.*.items.*.pricelist_id.exists' => 'El precio seleccionado para el ítem no existe.',
            'groups.*.items.*.quantity.required' => 'La cantidad del ítem es obligatoria.',
            'groups.*.items.*.quantity.numeric' => 'La cantidad del ítem debe ser un número.',
            'groups.*.items.*.quantity.min' => 'La cantidad mínima permitida para un ítem es 0.01.',
            'groups.*.items.*.unit_price.required' => 'El precio unitario del ítem es obligatorio.',
            'groups.*.items.*.unit_price.numeric' => 'El precio unitario debe ser un número.',
            'groups.*.items.*.unit_price.min' => 'El precio unitario mínimo es 0.',
            'groups.*.items.*.item_type.required' => 'El tipo de ítem es obligatorio.',
            'groups.*.items.*.item_type.enum' => 'El tipo de ítem seleccionado es inválido.',

            'items.array' => 'El formato de los ítems no es válido.',
            'items.*.pricelist_id.required' => 'El ítem debe tener un precio seleccionado.',
            'items.*.pricelist_id.exists' => 'El precio seleccionado para el ítem no existe.',
            'items.*.budget_code.max' => 'El código de presupuesto no puede exceder 50 caracteres.',
            'items.*.description.string' => 'La descripción del ítem debe ser texto.',
            'items.*.quantity.required' => 'La cantidad del ítem es obligatoria.',
            'items.*.quantity.numeric' => 'La cantidad del ítem debe ser un número.',
            'items.*.quantity.min' => 'La cantidad mínima permitida es 0.01.',
            'items.*.unit_price.required' => 'El precio unitario del ítem es obligatorio.',
            'items.*.unit_price.numeric' => 'El precio unitario debe ser un número.',
            'items.*.unit_price.min' => 'El precio unitario mínimo es 0.',
            'items.*.item_type.required' => 'El tipo de ítem es obligatorio.',
            'items.*.item_type.enum' => 'El tipo de ítem seleccionado es inválido.',
            'items.*.comment.string' => 'El comentario debe ser texto.',
        ];
    }
}
