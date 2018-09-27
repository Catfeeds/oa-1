<select class="js-select2-single form-control" name="{{ $name }}" id="{{ $name }}" style="width: 100%">
    @foreach($lists as $key => $value)
        <option value="{{ $key }}" @if ($key == $selected) selected @endif >
            {{ $value }}
        </option>
    @endforeach
</select>