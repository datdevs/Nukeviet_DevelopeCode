<!-- BEGIN: main -->
<!-- BEGIN: view -->
<div class="well">
    <form action="{NV_BASE_ADMINURL}index.php" method="get">
        <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}" /> <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" /> <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}" /> <input type="hidden" name="countryid" value="{ROW.countryid}" />
        <div class="row">
            <div class="col-xs-24 col-md-6">
                <div class="form-group">
                    <input class="form-control" type="text" value="{Q}" name="q" maxlength="255" placeholder="{LANG.search_title}" />
                </div>
            </div>
            <div class="col-xs-12 col-md-3">
                <div class="form-group">
                    <input class="btn btn-primary" type="submit" value="{LANG.search_submit}" />
                </div>
            </div>
        </div>
    </form>
</div>
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="w100">{LANG.weight}</th>
                    <th>{LANG.title}</th>
                    <th class="w150">{LANG.province_code}</th>
                    <th class="w150">{LANG.type}</th>
                    <th class="w100 text-center">{LANG.active}</th>
                    <th class="w150">&nbsp;</th>
                </tr>
            </thead>
            <!-- BEGIN: generate_page -->
            <tfoot>
                <tr>
                    <td class="text-center" colspan="6">{NV_GENERATE_PAGE}</td>
                </tr>
            </tfoot>
            <!-- END: generate_page -->
            <tbody>
                <!-- BEGIN: loop -->
                <tr>
                    <td><select class="form-control" id="id_weight_{VIEW.provinceid}" onchange="nv_change_weight('{VIEW.provinceid}', '{VIEW.countryid}');">
                            <!-- BEGIN: weight_loop -->
                            <option value="{WEIGHT.key}"{WEIGHT.selected}>{WEIGHT.title}</option>
                            <!-- END: weight_loop -->
                    </select></td>
                    <td><a href="{VIEW.link_district}" title="{VIEW.title}">{VIEW.title}</a> <span class="red">({VIEW.count})</span></td>
                    <td>{VIEW.code}</td>
                    <td>{VIEW.type}</td>
                    <td class="text-center"><input type="checkbox" name="status" id="change_status_{VIEW.provinceid}" value="{VIEW.provinceid}" {CHECK} onclick="nv_change_status({VIEW.provinceid});" /></td>
                    <td class="text-center"><i class="fa fa-edit fa-lg">&nbsp;</i> <a href="{VIEW.link_edit}#form">{LANG.edit}</a> - <em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="{VIEW.link_delete}" onclick="return confirm(nv_is_del_confirm[0]);">{LANG.delete}</a></td>
                </tr>
                <!-- END: loop -->
            </tbody>
        </table>
    </div>
</form>
<!-- END: view -->
<!-- BEGIN: error -->
<div class="alert alert-warning">{ERROR}</div>
<!-- END: error -->
<div id="form">
    <div class="panel panel-default">
        <div class="panel-body">
            <form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}&countryid={ROW.countryid}" method="post">
                <input type="hidden" name="provinceid" value="{ROW.provinceid}" />
                <div class="form-group">
                    <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.title}</strong> <span class="red">(*)</span></label>
                    <div class="col-sm-19 col-md-20">
                        <input class="form-control" type="text" name="title" value="{ROW.title}" required="required" oninvalid="setCustomValidity( nv_required )" oninput="setCustomValidity('')" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.alias}</strong></label>
                    <div class="col-sm-19 col-md-20">
                        <div class="input-group">
                            <input class="form-control" type="text" name="alias" value="{ROW.alias}" id="id_alias" /> <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-refresh fa-lg" onclick="nv_get_alias('id_alias');">&nbsp;</i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.country_c}</strong></label>
                    <div class="col-sm-19 col-md-20">
                        <select name="countryid" class="form-control">
                            <!-- BEGIN: country -->
                            <option value="{COUNTRY.countryid}"{COUNTRY.selected}>{COUNTRY.title}</option>
                            <!-- END: country -->
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.province_code}</strong></label>
                    <div class="col-sm-19 col-md-20">
                        <input class="form-control" type="text" name="code" value="{ROW.code}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.type}</strong></label>
                    <div class="col-sm-19 col-md-20">
                        <input class="form-control" type="text" name="type" value="{ROW.type}" />
                    </div>
                </div>
                <div class="form-group" style="text-align: center">
                    <input class="btn btn-primary" name="submit" type="submit" value="{LANG.save}" />
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
//<![CDATA[
	function nv_get_alias(id) {
		var title = strip_tags( $("[name='title']").val() ).toLowerCase();
		if (title != '') {
			$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=province&nocache=' + new Date().getTime(), 'get_alias_title=' + encodeURIComponent(title), function(res) {
				$("#"+id).val( strip_tags( res ) );
			});
		}
		return false;
	}
	function nv_change_weight(id, countryid) {
		var nv_timer = nv_settimeout_disable('id_weight_' + id, 5000);
		var new_vid = $('#id_weight_' + id).val();
		$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=province&nocache=' + new Date().getTime(), 'ajax_action=1&provinceid=' + id + '&countryid=' + countryid + '&new_vid=' + new_vid, function(res) {
			var r_split = res.split('_');
			if (r_split[0] != 'OK') {
				alert(nv_is_change_act_confirm[2]);
			}
			window.location.href = window.location.href;
			return;
		});
		return;
	}

	function nv_change_status(id) {
		var new_status = $('#change_status_' + id).is(':checked') ? true : false;
		if (confirm(nv_is_change_act_confirm[0])) {
			$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=province&nocache=' + new Date().getTime(), 'change_status=1&provinceid='+id, function(res) {
				var r_split = res.split('_');
				if (r_split[0] != 'OK') {
					alert(nv_is_change_act_confirm[2]);
				}
			});
		}
		else{
			$('#change_status_' + id).prop('checked', new_status ? false : true );
		}
		return;
	}


//]]>
</script>
<!-- BEGIN: auto_get_alias -->
<script type="text/javascript">
//<![CDATA[
	$("[name='title']").change(function() {
		nv_get_alias('id_alias');
	});
//]]>
</script>
<!-- END: auto_get_alias -->
<!-- END: main -->