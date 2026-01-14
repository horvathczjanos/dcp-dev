@extends('layouts.general')

@section('title', __('hello.assertion_fill_type_record'))


@section('content')

    <p class="text-center fs-1"> <strong>{{ __('hello.assertion_fill_type_record') }} </strong></p>



    <div class="row">
        <div class="col-12 text-center">

            <img src="data:image/png;base64,{{ base64_encode($badgeImage) }}" style="width: 150px;" alt="badge image">
            <p><strong>{{ $thisIssuerBadge->name }}</strong><br />
            <p><strong>Recipient: </strong>{{ $thisAssertion->originalIdentity }}<br />
                {{ $thisAssertion->issuedOn }}<br />
            </p>

        </div>
    </div>

    <?php
    $assertionType = json_decode($thisAssertion->assertiontype, true);
    ?>

    <div class="row">
        <div class="col-12 text-center">

            @if (!is_null($assertionType))
                You can fill type records for this assertion!
            @else
                You can not fill any type records for this assertion!
            @endif
        </div>
    </div>

    @if (!is_null($assertionType))

        <form method="POST" action="./../../recordAssertionType" enctype="multipart/form-data">
            @csrf
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <strong>{{ $message }}</strong>

                </div>
            @endif

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @foreach ($assertionType['form'] as $item)
                <div class="row typefields" id="{{ $item['id'] }}"
                    @if ($item['selected'] == 'false') style="display:none" @endif>
                    <h3>{{ $item['name'] }}</h3>
                    <i>{{ $item['description'] }}</i>
                    <ul>
                        @foreach ($item['fields'] as $field)
                            <li><strong>{{ $field['name'] }}</strong>
                                (<i>{{ $field['type'] }}</i>)<br />{{ $field['description'] }}<br />
                                <textarea style="resize: none;" name="{{ $field['id'] }}" class="form-control" id="{{ $field['id'] }}"
                                    rows="1">{{ $field['content'] }}</textarea>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <br />
            @endforeach



            <div class="container">
                <div class="alert alert-warning mt-5" role="alert">
                    <p class="text-center"><b>{{ __('hello.assertion_fill_type_records_text') }}</b></p>
                    <p class="text-center ">
                        <input type="submit" class="btn btn-success"
                            value="{{ __('hello.assertion_fill_type_records_button') }}" />
                    </p>
                </div>
            </div>

        </form>


    @endif


    <h2 class="section-title"><span>
            <p class="text-center"><a href="./../../showAssertions" class="btn btn-action">Back</a></p>

        </span></h2>



@endsection
