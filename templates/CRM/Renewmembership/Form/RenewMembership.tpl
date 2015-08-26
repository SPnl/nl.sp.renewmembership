<div class="crm-form-block crm-search-form-block">
    <div class="crm-member_search">
        <h1>{ts}Zoek op lidmaatschappen te vernieuwen{/ts}</h1>
        <table class="form-layout">
            <tr>
                <td><label>{ts}Membership Type(s){/ts}</label><br />
                    <div class="listing-box">
                        {foreach from=$form.member_membership_type_id item="membership_type_val"}
                            <div class="{cycle values='odd-row,even-row'}">
                                {$membership_type_val.html}
                            </div>
                        {/foreach}
                    </div>
                </td>
                <td><label>{ts}Membership Status{/ts}</label><br />
                    <div class="listing-box">
                        {foreach from=$form.member_status_id item="membership_status_val"}
                            <div class="{cycle values='odd-row,even-row'}">
                                {$membership_status_val.html}
                            </div>
                        {/foreach}
                    </div>
                </td>
            </tr>
            <tr><td><label>{ts}End Date{/ts}</label></td></tr>
            <tr>
                {include file="CRM/Core/DateRange.tpl" fieldName="member_end_date" from='_low' to='_high'}
            </tr>

            {if (isset($found))}
                <tr>
                <td>
                    Er zijn {$found} lidmaatschappen gevonden die verlengd gaan worden. <br>
                    Weet u het zeker?
                    <input type="hidden" name="continue" value="1" />
                </td>
                <td></td>
                </tr>
            {/if}

            <tr>
                <td colspan="2">{include file="CRM/common/formButtons.tpl"}</td>
            </tr>
        </table>
    </div>
</div>