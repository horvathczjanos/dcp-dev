@extends('layouts.general')

@section('title', __('editEarnerBadgePage.edit_earnerbadge_title'))


@section('content')

    <h2 class="section-title"><span>{{ __('editEarnerBadgePage.edit_earnerbadge_title') }} </span></h2>

    <form method="POST" action="./../../updateEarnerBadge" enctype="multipart/form-data">
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

        {{-- Memo --}}
        <div class="bd-callout bd-callout-info">
	    <p>{{ __('editEarnerBadgePage.edit_earnerbadge_description') }}</p>
	</div>
        <div class="mb-3">
            <label for="earnerbadge_description" class="form-label" required>{{ __('editEarnerBadgePage.edit_earnerbadge_desclabel') }}</label>
            <textarea name="earnerbadge_description" class="form-control" id="earnerbadge_description" rows="3"
                required>{{ $earnerbadge[0]['description'] }}</textarea>
        </div>

        {{-- Set  the recipient --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('editEarnerBadgePage.edit_earnerbadge_rcpt') }}</p>
	</div>
	<div class="mb-3">
        <select class="form-select" aria-label="Recipient type" name="recipient_type" disabled>
            <option value="email" @if ($earnerbadge[0]['recipientType'] == 'email') {{ 'selected' }} @endif>E-mail</option>
            <option value="phone" @if ($earnerbadge[0]['recipientType'] == 'phone') {{ 'selected' }} @endif>Phone number</option>
            <option value="url" @if ($earnerbadge[0]['recipientType'] == 'url') {{ 'selected' }} @endif>URL</option>
        </select>
	</div>
        <div class="mb-3">
            <label for="recipient_identity" class="form-label" required>{{ __('editEarnerBadgePage.edit_earnerbadge_rcpt_label') }}</label>
            <textarea style="resize: none;" name="recipient_identity" class="form-control" id="recipient_identity" rows="1"
                required>{{ $earnerbadge[0]['recipientIdentity'] }}</textarea>
        </div>

        {{-- Enable search --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('editEarnerBadgePage.edit_earnerbadge_isSearch') }}</p>
	</div>
        <div class="mb-3">
            <input type="checkbox" name="isSearchable" id="isSearchable" @if ($earnerbadge[0]['issearchable'] == '1') checked @endif>
            <label for="isSearchable">{{ __('editEarnerBadgePage.edit_earnerbadge_isSearch_label') }}</label>
        </div>


        @php
            
            $tagtext = '';
            $tags = json_decode($earnerbadge[0]['earnerBadgeTags']);
            foreach ($tags as $tag) {
                $tagtext .= $tag . '; ';
            }
            $tagtext = rtrim($tagtext, '; ');
        @endphp

        {{-- Editor field of tags --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('editEarnerBadgePage.edit_earnerbadge_tags') }}</p>
	</div>
        <div class="mb-3">
            <label for="earner_badge_tags" class="form-label">{{ __('editEarnerBadgePage.edit_earnerbadge_tags_label') }}</label>
            <textarea style="resize: none;" name="earner_badge_tags" class="form-control" id="earner_badge_tags"
                rows="1">{{ $tagtext }}</textarea>
        </div>



        {{-- Update button --}}
            <div class="bd-callout bd-callout-info">
                <p>{{ __('editEarnerBadgePage.edit_earnerbadge_info') }}</p>
            </div>
                <p class="text-center ">
		    <button type="submit" class="btn btn-primary" value="Save Changes, Click Here">
		        {{ __('editEarnerBadgePage.edit_earnerbadge_button') }} &nbsp;&nbsp;<i class="bi-save"></i>
		    </button>
                </p>

    </form>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="./../../earnerMain" class="btn btn-action">{{ __('general.back-to-earner')}}</a></p>
        </span></h2>


@endsection
