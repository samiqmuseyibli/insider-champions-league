@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="body pt-5">
            <div class="row pb-3">
                <div class="col-md-6">
                    <h5 class="mt-5 d-flex justify-content-center">League Table</h5>
                    <table class="table table-hover">
                        <thead class="thead">
                        <tr>
                            <th scope="col">Club</th>
                            <th scope="col">PTS</th>
                            <th scope="col">P</th>
                            <th scope="col">W</th>
                            <th scope="col">D</th>
                            <th scope="col">L</th>
                            <th scope="col">GD</th>
                            <th scope="col">Prediction</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($league->teams as $team)
                            <tr>
                                <th>{{ $team->name }}</th>
                                <th scope="row">{{ $team->points }}</th>
                                <td>{{ $team->win + $team->draw + $team->lost }}</td>
                                <td>{{ $team->win }}</td>
                                <td>{{ $team->draw }}</td>
                                <td>{{ $team->lost }}</td>
                                <td>{{ $team->gd }}</td>
                                <td>
                                    {{ $league->getChampionshipPercentage($league)[$team->id] }}
                                    %
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="">
                        <a href="{{ route('fixtures.play', ['week' => 'all']) }}" @if ($week >= 6) disabled="disabled"
                           @endif class="btn btn-secondary float-start">@if ($week < 6)
                                Play all
                            @else
                                Finished
                            @endif</a>
                        @if ($week < 6)
                            <a href="{{ route('fixtures.play', ['week' => $week]) }}"
                               class="btn btn-secondary float-end">Next week</a>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="mt-5 d-flex justify-content-center">{{ $league->name }} - Match Results</h5>
                    <table class="table table-hover">
                        <thead class="">
                        <tr>
                            <th scope="col">#Week</th>
                            <th scope="col">Fixtures</th>
                            <th scope="col">Scores</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($fixtures as $fixture)
                            <tr>
                                <th scope="row">{{ $fixture->week }}</th>
                                <td>{{ $fixture->homeTeam->name }} - {{ $fixture->awayTeam->name }}</td>
                                <td>
                                    <input id="home_team_{{ $fixture->id }}" type="text"
                                           value="{{ $fixture->home_team_goals ?? '-' }}"> :
                                    <input id="away_team_{{ $fixture->id }}" type="text"
                                           value="{{ $fixture->away_team_goals ?? '-' }}">
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-primary btn-right float-right btn-edit-score"
                                            data-id="{{ $fixture->id }}">
                                        edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(".btn-edit-score").click(function () {
            var id = $(this).data('id');
            var home = $("#home_team_" + id).val();
            var away = $("#away_team_" + id).val();

            let formData = new FormData();
            formData.append('fixture', id);
            formData.append('home', home);
            formData.append('away', away);

            fetch("{{ route('fixtures') }}", {
                method: "PUT",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: new URLSearchParams(formData)
            })
                .then(response => response.json())
                .then(function(result) {
                    if (result.success) {
                        location.reload();
                    }
                });
        });

    </script>
@endsection
