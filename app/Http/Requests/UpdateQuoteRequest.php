<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\QuoteItemType;

/**
 * Form Request para validar la actualización de una cotización.
 */
class UpdateQuoteRequest extends FormRequest
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
            'project_id' => 'sometimes|nullable|exists:projects,id',
            'request_number' => 'sometimes|nullable|string|max:255',
            'employee_id' => 'sometimes|nullable|exists:employees,id',
            'sub_client_id' => 'sometimes|required|exists:sub_clients,id',
            'quote_category_id' => 'sometimes|required|exists:quote_categories,id',
            'energy_sci_manager' => 'sometimes|nullable|string|max:255',
            'ceco' => 'sometimes|nullable|string|max:255',
            'project_name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|in:Pendiente,Enviado,Aprobado,Anulado',
            'quote_type' => ['sometimes', 'nullable', Rule::enum(\App\Enums\QuoteType::class)],
            'quote_date' => 'sometimes|nullable|date',
            'execution_date' => 'sometimes|nullable|date|after_or_equal:quote_date',

            // Grupos (usado por QuoteManager)
            'groups' => 'sometimes|nullable|array',
            'groups.*.name' => 'nullable|string|max:255',
            'groups.*.items' => 'nullable|array',
            'groups.*.items.*.pricelist_id' => 'required|exists:pricelists,id',
            'groups.*.items.*.quantity' => 'required|numeric|min:0.01',
            'groups.*.items.*.unit_price' => 'required|numeric|min:0',
            'groups.*.items.*.item_type' => ['required', Rule::enum(QuoteItemType::class)],
            'groups.*.items.*.comment' => 'nullable|string',

            // Items directos (formato plano)
            'items' => 'sometimes|nullable|array',
            'items.*.pricelist_id' => 'required|exists:pricelists,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.item_type' => ['required', Rule::enum(QuoteItemType::class)],
            'items.*.comment' => 'nullable|string',
        ];
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

            'sub_client_id.required' => 'Debe seleccionar un cliente / tienda para la cotización.',
            'sub_client_id.exists' => 'El cliente / tienda seleccionado no existe.',

            'quote_category_id.required' => 'Debe seleccionar una categoría para la cotización.',
            'quote_category_id.exists' => 'La categoría seleccionada no existe.',

            'request_number.max' => 'El número de solicitud no puede exceder 255 caracteres.',
            'employee_id.exists' => 'El empleado seleccionado no existe.',
            'energy_sci_manager.max' => 'El nombre del gerente no puede exceder 255 caracteres.',
            'ceco.max' => 'El CECO no puede exceder 255 caracteres.',
            'status.required' => 'El estado de la cotización es obligatorio.',
            'status.in' => 'El estado debe ser uno de: Pendiente, Enviado, Aprobado, Anulado.',
            'quote_date.date' => 'La fecha de cotización no es válida.',
            'execution_date.date' => 'La fecha de ejecución no es válida.',
            'execution_date.after_or_equal' => 'La fecha de ejecución debe ser igual o posterior a la fecha de cotización.',

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
