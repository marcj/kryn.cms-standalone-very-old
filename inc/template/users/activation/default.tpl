
{if $result.form}<form action="" method="post">{/if}
<dl>
    {if $result.failed}
    <dt>[[Activation failed, possible cause:]]</dt>
    <dd>
        <li>[[The email address or activation key was entered wrongly.]]</li>
        <li>[[The account is already activated]]</li>
    </dd>
    {/if}
    
    {if $result.form}
    <dt>[[Email address]]</dt>
    <dd><input type="text" name="e" value="{$data.email}" /></dd>
    
    <dt>[[Activation code]]</dt>
    <dd><input type="text" name="k" value="{$data.actkey}" /></dd>
    
    <dd><input type="submit" value="[[Activate]]" /></dd>
    {/if}
    
    {if $result.succes}
    <dt>[[Activation succesfull]]</dt>
    <dd>[[Thank you for activating your account.]]</dd>
    {if $result.admin}
    <dd>[[To use your account an administrator needs to activate your account]]</dd>
    {/if} 
    {/if}
</dl>
{if $result.form}</form>{/if}