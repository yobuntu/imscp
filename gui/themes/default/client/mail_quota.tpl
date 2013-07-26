<form name="quotaFrm" method="post" action="mail_quota.php?id={MAIL_ID}">
	<table class="firstColFixed">
		<thead class="ui-widget-header">
		<tr>
			<th colspan="2">{MAIL_ADDRESS}</th>
		</tr>
		</thead>
		<tbody class="ui-widget-content">
		<tr>
			<td><label for="quota">{TR_QUOTA}</label></td>
			<td><input name="quota" id="quota" type="text" value="{QUOTA}"/></td>
		</tr>
		</tbody>
	</table>
	<div class="buttons">
		<input name="submit" type="submit" value="{TR_UPDATE}"/>
		<input name="cancel" type="button" onclick="MM_goToURL('parent','mail_accounts.php');return document.MM_returnValue" value="{TR_CANCEL}"/>
	</div>
</form>
