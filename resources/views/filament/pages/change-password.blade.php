<x-filament-panels::page>

    <form wire:submit="submit">

        {{ $this->form }}


        <x-filament::actions 
            :actions="$this->getFormActions()"
            class="mt-6" 
        />
    </form> 

</x-filament-panels::page>