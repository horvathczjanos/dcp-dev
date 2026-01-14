@extends('layouts.general')

@section('title', __('adminEditIssuer.edit_issuer_title'))


@section('content')

    <h2 class="section-title"><span>{{ __('adminEditIssuer.edit_issuer_title') }}</span></h2>

    <form method="POST" action="./../../updateIssuer" enctype="multipart/form-data">
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
            <p>{{ __('adminEditIssuer.edit_issuer_name-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="issuer_name_text" class="form-label">{{ __('adminEditIssuer.edit_issuer_name') }}</label>
            <textarea style="resize: none;" name="issuer_name_text" class="form-control" id="issuer_name_text" rows="1" required>{{ $issuer->name }}</textarea>
        </div>

        {{-- custom fields --}}

        @if ($customFields && $customFields['header']['name'] == 'issuerCustomFileds')
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminEditIssuer.edit_issuer_custom-fields') }}</p>
        </div>

            @foreach ($customFields['form'] as $field)
                @if ($field['type'] == 'text')
                    <div class="mb-3">
                        <label for="{{ $field['id'] }}" class="form-label">{{ $field['name'] }}</label>
                        <textarea style="resize: none;" name="{{ $field['id'] }}" class="form-control" id="{{ $field['id'] }}"
                            rows="1" required>{{ $field['content'] }}</textarea>
                    </div>
                @else
                @endif
            @endforeach

        @endif


        {{-- URL of Issuer --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminEditIssuer.edit_issuer_url-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="issuer_url_text" class="form-label">{{ __('adminEditIssuer.edit_issuer_url') }}</label>
            <textarea style="resize: none;" name="issuer_url_text" class="form-control" id="issuer_url_text" rows="1" required>{{ $issuer->url }}</textarea>
        </div>

        {{-- email of Issuer --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminEditIssuer.edit_issuer_email-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="issuer_email_text" class="form-label">{{ __('adminEditIssuer.edit_issuer_email') }}</label>
            <textarea style="resize: none;" name="issuer_email_text" class="form-control" id="issuer_email_text" rows="1" required>{{ $issuer->email }}</textarea>
        </div>

        {{-- logo of Issuer --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminEditIssuer.edit_issuer_logo-lbl') }}</p>
        </div>
        <div class="mb-3">
            <?php $imageData64 = base64_encode(Storage::get('public/images/' . $issuer->logofile));
            $ext = pathinfo(storage_path() . '/public/images/' . $issuer->logofile, PATHINFO_EXTENSION);
            
            ?>
            <p>{{ __('adminEditIssuer.edit_issuer_curr-logo') }}: <img src="data:image/{{ $ext }};base64,{{ $imageData64 }}" style="width: 50px;"></p>
            <input type="file" name="file" class="custom-file-input" id="chooseFile">
            <label class="custom-file-label" for="chooseFile"> {{ __('adminEditIssuer.edit_issuer_logo-instruction') }} </label>
        </div>


        {{-- admin person of Issuer --}}

        <?php
        $filerForIssuers = App\Models\User::get()->filter(function ($value, $key) {
            if (in_array('issuer', $value['roles'])) {
                return true;
            }
        });
        
        ?>

        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminEditIssuer.edit_issuer_admin-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label class="custom-file-label" for="selectAdminIssuer"> {{ __('adminEditIssuer.edit_issuer_admin') }}
            </label>
            <select class="form-select" size="5" name="issuer_option" required>
                @foreach ($filerForIssuers as $aissuer)
                    <option value="{{ $aissuer->id }}" @if ($aissuer->id == $issuer->adminID) selected @endif>
                        {{ $aissuer->name }} [ {{ $aissuer->email }} ]
                    </option>
                @endforeach
            </select>
        </div>

        {{-- assistant person of Issuer --}}

        <?php
        $filerForIssuers = App\Models\User::get()->filter(function ($value, $key) {
            if (in_array('assistant', $value['roles'])) {
                return true;
            }
        });
        
        ?>

        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminEditIssuer.edit_issuer_assist-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label class="custom-file-label" for="selectAssistantIssuer"> {{ __('adminEditIssuer.edit_issuer_assist') }}
            </label>
            <select class="form-select" size="5" name="assistant_option[]" multiple="multiple" >
                @foreach ($filerForIssuers as $aissuer)
                    <option value="{{ $aissuer->id }}" @if (in_array($aissuer->id, json_decode($issuer->assistantID))) selected @endif>
                        {{ $aissuer->name }} [ {{ $aissuer->email }} ]
                    </option>
                @endforeach
            </select>
        </div>


        {{-- Update button --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminEditIssuer.edit_issuer_btn-lbl') }}</p>
	</div>
            <p class="text-center ">
                <button type="submit" class="btn btn-primary" value="update">
		{{ __('adminEditIssuer.edit_issuer_button') }}&nbsp;&nbsp;<i class="bi-check-circle-fill"></i>
		</button>
            </p>
        </div>

    </form>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="./../../manageIssuers" class="btn btn-action">{{ __('adminEditIssuer.edit_issuer_back-2-manis') }}</a></p>
        </span></h2>


@endsection
