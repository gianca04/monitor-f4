@props(['id', 'title', 'active' => false, 'icon' => ''])

<div id="tab-pane-{{ $id }}" 
     class="vanilla-tab-pane {{ $active ? 'active block' : 'hidden' }}" 
     data-tab-id="{{ $id }}" 
     data-tab-title="{{ $title }}" 
     data-tab-icon="{{ $icon }}"
     data-tab-active="{{ $active ? 'true' : 'false' }}">
    {{ $slot }}
</div>
