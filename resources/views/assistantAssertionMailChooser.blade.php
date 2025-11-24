@extends('layouts.general')

@section('title', __('assistantMailAssertionTemplates.mat_title'))


@section('content')

    <h2 class="section-title"><span>{{ __('assistantMailAssertionTemplates.mat_title') }}</span></h2>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bd-callout bd-callout-info">
        <p>{{ __('assistantMailAssertionTemplates.mat_sel-instruction') }}</p>
    </div>

    <form method="POST" action="{{ env('APP_URL') }}/doAssistantSendingAssertionByEmail2" enctype="multipart/form-data">
        @csrf




        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ __('assistantMailAssertionTemplates.mat_select') }}</th>
                    <th scope="col">{{ __('assistantMailAssertionTemplates.mat_templates') }}</th>


                </tr>
            </thead>
            <tbody>


                @foreach ($mailTemplate['body'] as $item)
                    <tr>
                        <td scope="col">{{ $loop->index + 1 }}</td>
                        <td scope="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mailTemplate" id="{{ $item['id'] }}"
                                    value="{{ $item['id'] }}">
                            </div>
                        </td>
                        <td>
                            <p>{{ $item['name'] }}
                                <br />
                                {{ $item['description'] }}
                            </p>
                        </td>

                    </tr>
                @endforeach

            </tbody>
        </table>

        <div class="bd-callout bd-callout-info">
            <p>{{ __('assistantMailAssertionTemplates.mat_send-instruction') }}</p>
        </div>
        <p class="text-center ">
            <button type="submit" class="btn btn-primary" value="creata">
                {{ __('assistantMailAssertionTemplates.mat_send-button') }}&nbsp;&nbsp;<i class="bi-save"></i>
            </button>
        </p>
        </div>

    </form>


    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/backtoassistantcompany" class="btn btn-action">{{ __('assistantMailAssertionTemplates.mat_back-2-inst') }}</a></p>
        </span></h2>



@endsection
