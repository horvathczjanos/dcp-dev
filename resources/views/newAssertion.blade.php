@extends('layouts.general')

@section('title', __('hello.new_assertion_title'))


@section('content')

    <p class="text-center fs-1"> <strong>{{ __('hello.new_assertion_title') }} </strong></p>


    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center"><b>{{ __('hello.all_badgeclass_information') }}</b> {{ $numberofbadgeclasses }}</p>
        </div>
    </div>

    <div class="container">
        <div class="alert alert-warning mt-5" role="alert">
            <p class="text-center"><b>{{ __('hello.select_badgeclass') }}</b></p>
        </div>
    </div>

    <form method="POST" action="doAssertion" enctype="multipart/form-data">
        @csrf

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ __('hello.badgeclass_select') }}</th>
                    <th scope="col">{{ __('hello.badgeclass_image') }}</th>
                    <th scope="col">{{ __('hello.badgeclass_shortinfo') }}</th>
                    <th scope="col">...</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($badgeclasslist as $item)
                    <tr>
                        <td scope="col">{{ $loop->index + 1 }}</td>
                        <td scope="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="badgeclass"
                                    id="{{ $item->badgeclassID }}" value="{{ $item->badgeclassID }}">
                            </div>

                        </td>
                        <td scope="col"><a
                                href="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                target="_blank" rel="noopener noreferrer"><img
                                    src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                    width="100px" alt="badge image"></a></td>
                        <td scope="col">
                            {{ $item->name }}<br />{{ $item->created_at }}<br />{{ $item->description }}</td>
                        <td><a class="btn btn-primary" href="/issuerbadges/{{ $item->badgeclassID }}/info"
                                role="button">Badgeclass Info</a></td>
                    </tr>
                @endforeach

            </tbody>
        </table>

        <div class="container">
            <div class="alert alert-warning mt-5" role="alert">
                <p class="text-center"><b>{{ __('hello.add_awarded_data') }}</b> </p>
            </div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control" id="email" required>
        </div>

        <div class="container">
            <div class="alert alert-warning mt-5" role="alert">
                <p class="text-center"><b>{{ __('hello.set_issuing_date') }}</b> </p>
            </div>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="datetime-local" name="date" class="form-control" id="date" required>
        </div>



        <div class="container">
            <div class="alert alert-warning mt-5" role="alert">
                <p class="text-center"><b>{{ __('hello.create_new_assertion_info') }}</b></p>
                <p class="text-center ">
                    <input type="submit" class="btn btn-success"
                        value="{{ __('hello.create_new_assertion_button') }}" />
                </p>
            </div>
        </div>

    </form>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/backtocompany" class="btn btn-action">Back to Company page</a></p>
        </span></h2>

@endsection
