<script type="text/javascript">
	/* <![CDATA[ */
	function action_delete(url, subject, object) {
		if (object == 'database') {
			msg = "{TR_DATABASE_MESSAGE_DELETE}"
		} else {
			msg = "{TR_USER_MESSAGE_DELETE}"
		}

		if (confirm(sprintf(msg, subject))) {
			location = url;
		}

		return false;
	}
	/* ]]> */
</script>

<!-- BDP: sql_databases_users_list -->
<table>
	<tr>
		<th colspan="3">{TR_DATABASE}</th>
	</tr>
	<!-- BDP: sql_databases_list -->
	<tr>
		<td style="width:250px"><strong>{DB_NAME}</strong></td>
		<td>
			<!-- BDP: sql_database_actions -->
			<a href="sql_user_add.php?id={DB_ID}" class="icon i_add_user" title="{TR_ADD_USER}">{TR_ADD_USER}</a>
			<a href="#" class="icon i_delete" onclick="return action_delete('sql_database_delete.php?id={DB_ID}', '{DB_NAME}', 'database')" title="{TR_DELETE}">{TR_DELETE}</a>
			<!-- EDP: sql_database_actions -->
		</td>
	</tr>
	<!-- BDP: sql_users -->
	<tr>
		<td colspan="3">
			<table style="border: 0;">
				<!-- BDP: sql_users_list -->
				<tr style="border: 0">
					<td style="width:240px; border: 0">{DB_USER}</td>
					<td style="border: 0">
						<!-- BDP: sql_user_actions -->
						<a href="pma_auth.php?id={USER_ID}" class="icon i_pma" target="{PMA_TARGET}" title="{TR_LOGIN_PMA}">{TR_PHPMYADMIN}</a>
						<a href="sql_change_password.php?id={USER_ID}" class="icon i_change_password" title="{TR_CHANGE_PASSWORD}">{TR_CHANGE_PASSWORD}</a>
						<a href="#" class="icon i_delete" onclick="return action_delete('sql_delete_user.php?id={USER_ID}', '{DB_USER}', 'user')" title="{TR_DELETE}">{TR_DELETE}</a>
						<!-- EDP: sql_user_actions -->
					</td>
				</tr>
				<!-- EDP: sql_users_list -->
			</table>
		</td>
	</tr>
	<!-- EDP: sql_users -->
	<!-- EDP: sql_databases_list -->
</table>
<!-- EDP: sql_databases_users_list -->
