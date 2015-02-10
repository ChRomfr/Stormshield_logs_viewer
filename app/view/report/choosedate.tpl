{foreach $date_available as $row}
<li><a href="{$Helper->getLink("report/index?date={$row.date}")}" title="">{$row.date}</a></li>
{/foreach}