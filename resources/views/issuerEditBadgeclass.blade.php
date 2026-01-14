@extends('layouts.general')

@section('title', __('issuerEditBadgeClass.ebc_title'))


@section('content')

    <h2 class="section-title"><span>{{ __('issuerEditBadgeClass.ebc_title') }}</span></h2>

    <form method="POST" action="editBadgeclass" enctype="multipart/form-data">
        @csrf


        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <strong>{{ $message }}</strong>
                <br />
                {{ __('issuerEditBadgeClass.ebc_success-id') }}: {{ Session::get('id') }}
                <br />
                {{ __('issuerEditBadgeClass.ebc_success-badge-info') }}: {{ Session::get('badge') }}
                <br />
                {{ __('issuerEditBadgeClass.ebc_success-issued-on') }}: {{ Session::get('issuedOn') }}
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



        <div class="bd-callout bd-callout-info">
            <p>{{ __('issuerEditBadgeClass.ebc_name-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="badgeclass_name" class="form-label" required>{{ __('issuerEditBadgeClass.ebc_name') }}</label>
            <input type="text" name="badgeclass_name" class="form-control" id="badgeclass_name"
                value="{{ $thisBadgeClass['name'] }}" required>
        </div>

        <div class="bd-callout bd-callout-info">
            <p>{{ __('issuerEditBadgeClass.ebc_desc-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="badgeclass_description" class="form-label"
                required>{{ __('issuerEditBadgeClass.ebc_desc') }}</label>
            <textarea name="badgeclass_description" class="form-control" id="badgeclass_description" rows="3" required>{{ $thisBadgeClass['description'] }}</textarea>
        </div>

        <div class="bd-callout bd-callout-info">
            <p>{{ __('issuerEditBadgeClass.ebc_criteria-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="badgeclass_criteria" class="form-label"
                required>{{ __('issuerEditBadgeClass.ebc_criteria') }}</label>
            <textarea name="badgeclass_criteria" class="form-control" id="badgeclass_criteria" rows="3" required>{{ $thisBadgeClass['criteria'] }}</textarea>
        </div>

        {{-- Badge tags --}}
        @php
            
            $tagtext = '';
            $tags = json_decode($thisBadgeClass['tags']);
            foreach ($tags as $tag) {
                $tagtext .= $tag . '; ';
            }
            $tagtext = rtrim($tagtext, '; ');
        @endphp



        <div class="bd-callout bd-callout-info">
            <p>{{ __('issuerEditBadgeClass.ebc_tags-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="badgeclass_tags" class="form-label" required>{{ __('issuerEditBadgeClass.ebc_tags') }}</label>
            <input type="text" name="badgeclass_tags" class="form-control" id="badgeclass_tags"
                value="{{ $tagtext }}" required>
        </div>

        {{-- Image of badgeclass --}}


        <?php
        $imageData64 = base64_encode(Storage::get($thisBadgeImage->filepath));
        $ext = pathinfo(storage_path() . $thisBadgeImage->filepath, PATHINFO_EXTENSION);
        
        ?>

        <div class="bd-callout bd-callout-info">
            <p>{{ __('issuerEditBadgeClass.ebc_badge-img-lbl') }}</p>
        </div>
        <div class="custom-file">
            <p>{{ __('issuerEditBadgeClass.ebc_cur-badge-img') }}: <img
                    src="data:image/{{ $ext }};base64,{{ $imageData64 }}" style="width: 50px;"></p>
            <input type="file" name="file" class="custom-file-input" id="chooseFile">
            <label class="custom-file-label" for="chooseFile"
                >{{ __('issuerEditBadgeClass.ebc_badge-img') }}</label>
            <p>&nbsp;</p>
        </div>

        {{-- Image author name --}}
        <div class="mb-3">
            <label for="badgeclass_image_author_name"
                class="form-label">{{ __('issuerEditBadgeClass.ebc_badge-img-auth-name') }}</label>
            <input type="text" name="badgeclass_image_author_name" class="form-control" id="badgeclass_image_author_name"
                value="{{ $thisBadgeImage->imgauthorname }}">
        </div>

        {{-- Image author email --}}
        <div class="mb-3">
            <label for="badgeclass_image_author_email"
                class="form-label">{{ __('issuerEditBadgeClass.ebc_badge-img-auth-email') }}</label>
            <input type="text" name="badgeclass_image_author_email" class="form-control"
                id="badgeclass_image_author_email" value="{{ $thisBadgeImage->imgauthoremail }}">
        </div>

        {{-- Image author url --}}
        <div class="mb-3">
            <label for="badgeclass_image_author_url"
                class="form-label">{{ __('issuerEditBadgeClass.ebc_badge-img-auth-url') }}</label>
            <input type="text" name="badgeclass_image_author_url" class="form-control" id="badgeclass_image_author_url" value="{{ $thisBadgeImage->imgauthorurl }}">
        </div>

        {{-- Image caption --}}
        <div class="mb-3">
            <label for="badgeclass_image_caption"
                class="form-label">{{ __('issuerEditBadgeClass.ebc_badge-img-capt') }}</label>
            <input type="text" name="badgeclass_image_caption" class="form-control" id="badgeclass_image_caption" value="{{ $thisBadgeImage->imgcaption }}">
        </div>

        <div class="bd-callout bd-callout-info">
            <p>{{ __('issuerEditBadgeClass.ebc_btn-lbl') }}</p>
        </div>
        <p class="text-center ">
            <button type="submit" class="btn btn-primary" value="create">
                {{ __('issuerEditBadgeClass.ebc_btn') }}&nbsp;&nbsp;<i class="bi-save"></i>
            </button>
        </p>
        </div>

    </form>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/backtocompany"
                    class="btn btn-action">{{ __('issuerEditBadgeClass.ebc_back-2-inst') }}</a></p>
        </span></h2>

@endsection
