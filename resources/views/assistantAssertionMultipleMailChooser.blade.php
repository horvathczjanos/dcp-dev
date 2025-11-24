@extends('layouts.general')

@section('title', __('assistantAssertionMultipleMailChooser.ammc_page-title'))


@section('content')

    <h2 class="section-title"><span>{{ __('assistantAssertionMultipleMailChooser.ammc_page-title') }}</span></h2>

    <div class="bd-callout bd-callout-info">
        <p>{{ __('assistantAssertionMultipleMailChooser.ammc_mail-template-info') }}</p>
    </div>

    @if (count($errors) > 0)
    
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form method="POST" action="{{ env('APP_URL') }}/doAssistantSendingAssertionByMultipleEmail" enctype="multipart/form-data">
        @csrf




        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ __('assistantAssertionMultipleMailChooser.th-sel') }}</th>
                    <th scope="col">{{ __('assistantAssertionMultipleMailChooser.th-templ-descr') }}</th>
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
            <p>{{ __('assistantAssertionMultipleMailChooser.btn-send-info') }}</p>
        </div>
        <p class="text-center ">
            <button type="submit" class="btn btn-primary" value="creata">
                {{ __('assistantAssertionMultipleMailChooser.btn-send') }}&nbsp;&nbsp;<i class="bi-save"></i>
            </button>
        </p>
        </div>

    </form>


    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/backtoassistantcompany" class="btn btn-action">{{ __('assistantAssertionMultipleMailChooser.back-2-inst') }}</a></p>
        </span></h2>



@endsection
