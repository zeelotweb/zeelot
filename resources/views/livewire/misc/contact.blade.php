<div class="mt-18">

<flux:field class="my-3">
    <flux:label>Name</flux:label>

    <flux:input wire:model="name" type="name" class="border border-slate-800 rounded-lg" />

    <flux:error name="name" />
 </flux:field>



<flux:field class="my-3">
    <flux:label>Email</flux:label>

    <flux:input wire:model="email" type="email" class="border border-slate-800 rounded-lg" />

    <flux:error name="email" />
 </flux:field>



 <flux:field class="my-3">
     <flux:label>Subject</flux:label>
<flux:select wire:model="subject" placeholder="Choose a Subject...">
    <flux:select.option>Sales</flux:select.option>
    <flux:select.option>Content Management</flux:select.option>
    <flux:select.option>Design services</flux:select.option>
    <flux:select.option>Web development</flux:select.option>
    <flux:select.option>Account</flux:select.option>
    <flux:select.option>Request Services</flux:select.option>
    <flux:select.option>Consulting</flux:select.option>
    <flux:select.option>Other</flux:select.option>
</flux:select>
<flux:error name="subject" />
 </flux:field>


<flux:field class="my-3">

<flux:textarea
    label="Message"
    placeholder="Be on point, lets know how we may help 500 words max..."
/>

    <flux:error name="message" />
 </flux:field>





<flux:button variant="primary" color="emerald" wire:click="send" 
            class="float-right hover:bg-stone-400 mt-4 cursor-pointer">
            Send
</flux:button>
</div>





