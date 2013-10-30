(function ($){
  var defaults = {
    url:'./demo.php',
    submiturl:'./submit.php',
    maxrows : 100
  };

  $.fn.byrexcel = function (options){
    var options = $.extend(defaults, options);
    var byrexcel = $(this);
    var modified_content = {};
    return this.each(function (){
      byrexcel.addClass("byrexcel");
      $.getJSON(options.url, function(retdata){
        modified_content.ID = retdata.ID;
        var table_start = "<table class=\"byrexcel-table\">";
        var table_head = "<tr class=\"byrexcel-tr\">";
        for(i in retdata.HEADER){
          table_head += "<th class=\"byrexcel-th\">" + retdata.HEADER[i] +  "</th>";
        }
        table_head += "</tr>";

        var table_content = "";

        for(i in retdata.CONTENT){
          if(i > options.maxrows){
            break;
          }
          table_content += "<tr>";
            for(j in retdata.CONTENT[i]){
              table_content += "<td class=\"byrexcel-td\" data-row=\""+i+"\" data-column=\""+(parseInt(j)+1)+"\">" + retdata.CONTENT[i][j] + "</td>";
            }
          table_content += "</tr>";
        }

        var table_end = "</table>";
        var table = table_start + table_head + table_content + table_end;

        byrexcel.append(table);
        var error_start = "<ul class=\"byrexcel-errorlist\">";
        var error_content = "";
        for(i in retdata.ERROR){
          if(retdata.ERROR[i].type == 'column'){
            error_content += "<li class=\"byrexcel-error\">"+retdata.ERROR[i].value + "</li>";
          }
          else if(retdata.ERROR[i].type == 'element'){
            error_content += "<li class=\"byrexcel-error\" error-row=\""+ retdata.ERROR[i].row +"\" error-col=\"" + (parseInt(retdata.ERROR[i].col) + 1) + "\">ROW " + retdata.ERROR[i].row + " COL "+ (parseInt(retdata.ERROR[i].col) + 1) + " ERROR: " + retdata.ERROR[i].value + " TIPS:" + retdata.ERROR[i].tips + "</li>";
            byrexcel.find('td[data-row=' + retdata.ERROR[i].row + '][data-column=' + (parseInt(retdata.ERROR[i].col) + 1) + ']').addClass('byrexcel-error');
          }
          else if(retdata.ERROR[i].type == 'special'){
            error_content += "<li class=\"byrexcel-error\">" + retdata.ERROR[i].value + "</li>";
          }
          
        }
        var error_end = "</ul>";
        var error = error_start + error_content + error_end;
        byrexcel.prepend(error);

        var error_row, error_col;
        $('.byrexcel-error').click(function() {
          error_row = $(this).attr('error-row');
          error_col = $(this).attr('error-col');
          byrexcel.find('td[data-row=' + error_row + '][data-column=' + error_col + ']').animate({backgroundColor:'red'}, 400).animate({backgroundColor:'white'}, 400).animate({backgroundColor:'red'}, 400).animate({backgroundColor:'white'}, 400);
        });

        var inputdiv = "<div id=\"byrexcel-inputdiv\" style=\"display:none;\"><input id=\"byrexcel-input\" type=\"text\" style=\"position:fixed;\"/></div>";
        var submitbtn = "<button id=\"byrexcel-submit\">提交</button>";
        var modified_items = new Array();
        byrexcel.append(inputdiv).append(submitbtn);
        var byrexcel_tdobj;
        var td_offset, td_height, td_width, td_content, td_col, td_row;
        $('.byrexcel-td').click(function(){
          byrexcel_tdobj = $(this);
          if( !(byrexcel_tdobj).hasClass('byrexcel-error') ) return;
          td_offset = $(this).offset();
          td_height = $(this).height();
          td_width = $(this).width();
          td_content = $(this).html();
          td_col = parseInt($(this).attr('data-column')) - 1;
          td_row = parseInt($(this).attr('data-row'));
          $('#byrexcel-inputdiv').show();
          $('#byrexcel-input').css({width: td_width, height: td_height, left: td_offset.left, top: td_offset.top}).val(td_content).trigger('focus').focusout(function(){
            var input_val = $('#byrexcel-input').val();
            byrexcel_tdobj.html(input_val);
            var exists = false;
            for( i in modified_items ){
              if(modified_items[i].row == td_row && modified_items[i].col == td_col){
                modified_items[i].value = input_val;
                exists = true;
              }      
            }
            if(!exists){
              modified_items.push({row:td_row, col:td_col, value:input_val});
            }
            $('#byrexcel-inputdiv').hide();
          });
        });
        $('#byrexcel-submit').click(function() {
          modified_content.items = modified_items;
          modified_items = [];
          $.post(options.submiturl, {excel_data : modified_content}, function(retdata){
            table_head = "<tr class=\"byrexcel-tr\">";
            for(i in retdata.HEADER){
              table_head += "<th class=\"byrexcel-th\">" + retdata.HEADER[i] +  "</th>";
            }
            table_head += "</tr>";

            table_content = "";

            for(i in retdata.CONTENT){
              if(i > options.maxrows){
                break;
              }
              table_content += "<tr>";
              for(j in retdata.CONTENT[i]){
                table_content += "<td class=\"byrexcel-td\" data-row=\""+i+"\" data-column=\""+(parseInt(j)+1)+"\">" + retdata.CONTENT[i][j] + "</td>";
              }
              table_content += "</tr>";
            }
            $('.byrexcel-table').html(table_head+table_content);

            error_content = "";
            for(i in retdata.ERROR){
              if(retdata.ERROR[i].type == 'column'){
                error_content += "<li class=\"byrexcel-error\">"+retdata.ERROR[i].value + "</li>";
              }
              else if(retdata.ERROR[i].type == 'element'){
                error_content += "<li class=\"byrexcel-error\" error-row=\""+ retdata.ERROR[i].row +"\" error-col=\"" + (parseInt(retdata.ERROR[i].col) + 1) + "\">ROW " + retdata.ERROR[i].row + " COL "+ (parseInt(retdata.ERROR[i].col) + 1) + " ERROR: " + retdata.ERROR[i].value + " TIPS:" + retdata.ERROR[i].tips + "</li>";
                byrexcel.find('td[data-row=' + retdata.ERROR[i].row + '][data-column=' + (parseInt(retdata.ERROR[i].col) + 1) + ']').addClass('byrexcel-error');
              }
              else if(retdata.ERROR[i].type == 'special'){
                error_content += "<li class=\"byrexcel-error\">" + retdata.ERROR[i].value + "</li>";
              }
            }
            $('.byrexcel-errorlist').html(error_content);

            error_row, error_col;
            $('.byrexcel-error').click(function() {
              error_row = $(this).attr('error-row');
              error_col = $(this).attr('error-col');
              byrexcel.find('td[data-row=' + error_row + '][data-column=' + error_col + ']').animate({backgroundColor:'red'}, 400).animate({backgroundColor:'white'}, 400).animate({backgroundColor:'red'}, 400).animate({backgroundColor:'white'}, 400);
            });


            $('.byrexcel-td').click(function(){
              byrexcel_tdobj = $(this);
              if( !(byrexcel_tdobj).hasClass('byrexcel-error') ) return;
              td_offset = $(this).offset();
              td_height = $(this).height();
              td_width = $(this).width();
              td_content = $(this).html();
              td_col = parseInt($(this).attr('data-column')) - 1;
              td_row = parseInt($(this).attr('data-row'));
              $('#byrexcel-inputdiv').show();
              $('#byrexcel-input').css({width: td_width, height: td_height, left: td_offset.left, top: td_offset.top}).val(td_content).trigger('focus').focusout(function(){
                input_val = $('#byrexcel-input').val();
                byrexcel_tdobj.html(input_val);
                exists = false;
                for( i in modified_items ){
                  if(modified_items[i].row == td_row && modified_items[i].col == td_col){
                    modified_items[i].value = input_val;
                    exists = true;
                  }      
                }
                if(!exists){
                  modified_items.push({row:td_row, col:td_col, value:input_val});
                }
                $('#byrexcel-inputdiv').hide();
                
              });
            });
            if(retdata == true)
              alert("success");
          }, 'json');
        });
      });
    });
  }
})(jQuery);
