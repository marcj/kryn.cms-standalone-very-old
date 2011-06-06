<p>System notification:</p>
<p>A new user has registered on <strong>{$domain.domain}</strong> with email address <strong>{$values.email}</strong>.</p>
<p>Registered values are:
    <table style="font-size: 12px; margin-left: 10px;">
    {foreach from=$values key=k item=v}
        {if $v ne ""}
        <tr>
            <td>{$k}</td>
            <td>{if $k eq "created"}{$v|date_format:"%d.%m.%Y %H:%M"}{else}{$v}{/if}</td>
        </tr>
        {/if}
    {/foreach}
    </table>
</p>
<p>
    Kind regards,<br />
    The registration system
</p>