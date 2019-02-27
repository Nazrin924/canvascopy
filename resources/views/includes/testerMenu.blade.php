<div class="text-center">

	<p style="margin: 0px">Tester options menu:</p>
	@unless(session()->get("maskedNetid"))
	<button onclick="location='{{URL::route('tester')}}?action=toggleUser'">Toggle createUser permission</button><br><br>
	<button onclick="location='{{URL::route('tester')}}?action=toggleSite'">Toggle createSite permission</button><br>
    @endunless
{{--	<button onclick="location='{{URL::route('tester')}}?action=add&user='+prompt('User NetID')">Add tester</button>
	<button onclick="location='{{URL::route('tester')}}?action=remove&user='+prompt('User NetID')">Remove tester</button>
	<p>
		Current Testers: @for($i=0; $i < count($testers); $i++){{$testers[$i]}}@if($i+1 != count($testers)), @endif @endfor
	</p>--}}
	<form action="{{URL::route('tester')}}">
		{{session()->get('errorMsg')}}
		<fieldset>
		<legend>Impersonate a Netid:</legend>
		<input type="text" name="maskNetid"><br>
		<input type="radio" name="maskRealm" value="CIT.CORNELL.EDU" checked> CU<br>
		<input type="radio" name="maskRealm" value="A.WCMC-AD.NET"> Weill<br>
		<input type="submit" value="Submit">
		</fieldset>
	</form>

</div>