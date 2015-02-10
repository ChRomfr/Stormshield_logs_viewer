{strip}
<option value=""></option>
{foreach $days as $day}
	<option value="{$day.day}" {if $date == $day.day}selected="selected"{/if}>{$day.day}</option>
{/foreach}
{/strip}