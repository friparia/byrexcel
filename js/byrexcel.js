/**
 * 需要jquery以及dragtable的js支持
 * 建议使用bootstrap风格
 * 一下三个url是必须自行设置
 * geturl 是BYRExcel::json_data()
 * changeurl 是列改变后的BYRExcel::change()
 * editurl 是单元格改变后的BYRExcel::edit()
 **/
geturl = 'get.php';
changeurl = 'change.php';
editurl = 'edit.php';

function render(){
	$.get(geturl,{}, function(data){
		//render head
		var head = '';
		for(var i = 0; i != data.HEAD.length; i++)
		{
			head += '<th>'+data.HEAD[i].NAME+'</th>';
		}

		headtable = '<table id=\"head\" class=\"table table-bordered\" style=\"margin-bottom:0px;border-bottom:0px;\"> ' +
			'<tr>' +
			head
		'</tr>' +
			'</table>';
		$('#excel-head').html(headtable);
		//render table content
		var content = '';
		var flag = 0;
		var column = 0;
		var rows = 0;
		if( data.LEAST_ROW == -1)
			rows = data.ROWS;
		else
			rows = data.LEAST_ROW;
		for(var i = 1; i <= rows; i++)
		{
			content += '<tr>';
			if( data.LEAST_ROW == -1)
				k = i;
			else
				k = i-1;
			for( var j = 0; j != data.CONTENT[k].length; j++)
			{
				if(data.CONTENT[k][j].check)
				{
					if(flag == 0)
						content += '<th id='+column.toString()+'>';
					else
						content += '<td id=\"'+j+','+i+'\">';
				}
				else
				{
					if(flag == 0)
						content += '<th id='+column.toString()+'>';
					else
						content += '<td id=\"'+j+','+i+'\" class=\"error\">';
				}
				content += data.CONTENT[k][j].val;
				if(flag == 0)
					content += '</th>';
				else
					content += '</td>';
				column++;
			}
			content += '</tr>';
			flag++;
		}
		$('#excel-content').html('<table id=\"excel\" class=\"table table-bordered\">'+content+'</table>');
		$('#excel').dragtable({
			persistState : function(table){
				table.el.find('th').each(function (i){
					if(this.id != '') {table.sortOrder[this.id]=i;} 
				});
				$.post(changeurl,{data:table.sortOrder}, function(data){
					render();
				},'json');
			}
		});
		refreshThWidth();	
		$('.error').click(function(e){
			var id = $(this).attr("id");
			$(this).unbind("click");
			var value = $(this).html();
			var input = "<input type=\"text\" id=\"edit\" value=\""+value+"\"/>";
			$(this).html(input);
			$(this).removeClass("error");
			$('#edit').trigger("focus");
			refreshThWidth();
			$('#edit').focusout(function() {
				var content = $('#edit').val();
				$.post(editurl, {id:id, content:content}, function(ret){
					render();
				});
			});
		});
	},'json');
}

function refreshThWidth(){
	var length = $('#excel th').size();
	for(var i=0; i!=length; i++)
	{
		$('#head th').eq(i).width($('#excel th').eq(i).width());
	}
}
render();
