{{-- Elenco errori di validazione dei form. Uso: <x-validation-errors /> (margine inferiore opzionale: :mb="4") --}}
@props(['mb' => 3])

@if($errors->any())
    <div class="alert alert-danger rounded-3 mb-{{ $mb }}">
        <ul class="m-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
