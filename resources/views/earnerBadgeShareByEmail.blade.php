@extends('layouts.general')

@section('title', __('earnerBadgeShareByEmail.earner_badgeshare_title'))


@section('content')

    <h2 class="section-title"><span>{{ __('earnerBadgeShareByEmail.earner_badgeshare_title') }}</span></h2>

    <form method="POST" action="{{ env('APP_URL') }}/doShareByEmail" enctype="multipart/form-data">
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

        <?php
        
        if (is_array($backData['checkOB']['badgeClass']['image'])) {
            $imageURL = $backData['checkOB']['badgeClass']['image']['id'];
        } else {
            $imageURL = $backData['checkOB']['badgeClass']['image'];
        }
        
        ?>

        <p>
            {{-- <a href="{{ $backData['checkOB']['badgeClass']['image'] }}"><img
                src="{{ $backData['checkOB']['badgeClass']['image'] }}" style="width: 150px;" alt="badge image"></a> --}}

            <a href="{{ $imageURL }}"><img src="{{ $imageURL }}" style="width: 150px;" alt="badge image"></a>
        </p>


        {{-- email of Recivier --}}
        <div class="mb-3">
            <label for="user_email_text" class="form-label">{{ __('earnerBadgeShareByEmail.earner_badgeshare_to-email') }}</label>
            <textarea style="resize: none;" name="user_email_text" class="form-control" id="user_email_text" rows="1"
                required></textarea>
        </div>

        {{-- Send button --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('earnerBadgeShareByEmail.earner_badgeshare_description') }}</p>
        </div>
        <p class="text-center">
            <button type="submit" class="btn btn-primary" value="update">
                {{ __('earnerBadgeShareByEmail.earner_badgeshare_action-btn') }} &nbsp;&nbsp;<i class="bi-save"></i>
            </button>
        </p>
        </div>
    </form>

    <h2 class="section-title"><span>
            <a href="{{ env('APP_URL') }}/earnerMain" class="btn btn-action">{{ __('general.back-to-earner') }}</a>
        </span></h2>

@endsection
