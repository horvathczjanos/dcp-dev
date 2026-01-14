<div>

    <button class="btn btn-success" wire:click="engine_dryrun" {{ $flag === 1 ? 'disabled' : '' }}>DRY Run!</button>
    <button class="btn btn-success" wire:click="engine_realrun" {{ $flag === 1 ? 'disabled' : '' }}>Award!</button>
    <button class="btn btn-success" wire:click="view_assertions" >View Assertions</button>

    <p>
        <textarea wire:model='standardMessage' rows="4" cols="100" readonly> </textarea>
    </p>


</div>
