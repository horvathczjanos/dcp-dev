@extends('layouts.general')

@section('title', __('adminNewIssuer.add_newissuer_title'))


@section('content')

    <h2 class="section-title"><span>{{ __('adminNewIssuer.add_newissuer_title') }}</span></h2>

    <form method="POST" action="./../../addNewIssuer" enctype="multipart/form-data">
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


        {{-- Name of Issuer --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminNewIssuer.add_newissuer_name-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="issuer_name_text" class="form-label">{{ __('adminNewIssuer.add_newissuer_name') }}</label>
            <textarea style="resize: none;" name="issuer_name_text" class="form-control" id="issuer_name_text" rows="1" required></textarea>
        </div>

        <?php
        $customFields = json_decode(Storage::get('public/issuercustomfields.json'));

        ?>

        {{-- custom fields of Issuer --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminNewIssuer.add_newissuer_custom-fields') }}</p>
        </div>

        @foreach ($customFields->form as $field)
            @if ($field->type == 'text')
                <div class="mb-3">
                    <label for="{{ $field->id }}" class="form-label">{{ $field->name }}</label>
                    <textarea style="resize: none;" name="{{ $field->id }}" class="form-control" id="{{ $field->id }}"
                        rows="1" required >{{ $field->content }}</textarea>
                </div>
            @else
            @endif
        @endforeach


        {{-- URL of Issuer --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminNewIssuer.add_newissuer_url-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="issuer_url_text" class="form-label">{{ __('adminNewIssuer.add_newissuer_url') }}</label>
            <textarea style="resize: none;" name="issuer_url_text" class="form-control" id="issuer_url_text" rows="1" required></textarea>
        </div>

        {{-- email of Issuer --}}
        <div class="bd-callour bd-callout-info">
            <p>{{ __('adminNewIssuer.add_newissuer_email-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="issuer_email_text" class="form-label">{{ __('adminNewIssuer.add_newissuer_email') }}</label>
            <textarea style="resize: none;" name="issuer_email_text" class="form-control" id="issuer_email_text" rows="1"
                required></textarea>
        </div>

        {{-- logo of Issuer --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminNewIssuer.add_newissuer_logo-lbl') }}</p>
        </div>
        <div class="mb-3">
            <input type="file" name="file" class="custom-file-input" id="chooseFile" required>
            <label class="custom-file-label" for="chooseFile"> {{ __('adminNewIssuer.add_newissuer_logo-instruction') }} </label>
        </div>

        <?php
        $filerForIssuers = App\Models\User::get()->filter(function ($value, $key) {
            if (in_array('issuer', $value['roles'])) {
                return true;
            }
        });
        
        ?>

        {{-- admin person of Issuer --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminNewIssuer.add_newissuer_admin-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label class="custom-file-label" for="selectAdminIssuer"> {{ __('adminNewIssuer.add_newissuer_admin') }}
            </label>
            <select class="form-select" size="5" name="issuer_option" required>
                @foreach ($filerForIssuers as $issuer)
                    <option value="{{ $issuer->id }}">{{ $issuer->name }} [ {{ $issuer->email }} ]</option>
                @endforeach
            </select>

        </div>

        {{-- Update button --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminNewIssuer.add_newissuer_btn-lbl') }}</p>
	</div>
            <p class="text-center">
                <button type="submit" class="btn btn-primary" value="create">
		{{ __('adminNewIssuer.add_newissuer_button') }}&nbsp;&nbsp;<i class="bi-save"></i>
		</button>
            </p>
        </div>

    </form>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="./../../manageIssuers" class="btn btn-action">{{ __('adminNewIssuer.add_newissuer_back-2-manis') }}</a></p>
        </span></h2>


@endsection
