@extends('layouts.app')

@section('content')
    <div class="content mt-5">
        <h3 class="section-heading">
            Create new League
        </h3>
        <p>Note: The teams will be selected randomly from the following list of teams, and all tours and tour fixtures
            will be prearranged prior to the event.</p>
        <div class="teams">
            <ul>
                @foreach($teams as $team)
                    <li>{{ $team }}</li>
                @endforeach
            </ul>
        </div>
        <div class="clearfix">
            <span class="btn btn-primary" id="create">Create</span>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Listen for the click event on the element with id "create"
        $("#create").click(function () {
            // Send a POST request to the "league" route
            fetch("{{ route('league') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
                .then(response => response.json()) // Parse the response as JSON
                .then(function (result) {
                    // Check if the response was successful
                    if (result.success === true) {
                        // Redirect to the "fixtures" route
                        window.location.href = "{{ route('fixtures') }}";
                    }
                });
        });
    </script>
@endsection
