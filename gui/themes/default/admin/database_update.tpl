
			<!-- BDP: database_updates -->
			<form name='database_update' action='database_update.php' method='post'>
				<table class="descriptions firstColFixed">
					<thead class="ui-widget-header">
					<tr>
						<th>{TR_DATABASE_UPDATES}</th>
						<th>{TR_DATABASE_UPDATE_DETAIL}</th>
					</tr>
					</thead>
					<tbody class="ui-widget-content">
					<!-- BDP: database_update -->
					<tr>
						<td>{DB_UPDATE_REVISION}</td>
						<td>{DB_UPDATE_DETAIL}</td>
					</tr>
					<!-- EDP: database_update -->
					</tbody>
				</table>
				<div class="buttons">
					<input type="hidden" name="uaction" id='execute' value="update" />
					<input type="submit" name="submit" value="{TR_PROCESS_UPDATES}" />
				</div>
			</form>
			<!-- EDP: database_updates -->
