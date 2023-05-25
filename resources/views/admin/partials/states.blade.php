<option value="">----Select State----</option>

@foreach($states as $state)
<option value="{{$state->id}}">{{$state->name}}</option>
@endforeach