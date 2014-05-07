<div class="form-group">
    @if ($label)
        {{ Form::label($name, $label) }}
    @endif
    {{ $html }}
</div>
