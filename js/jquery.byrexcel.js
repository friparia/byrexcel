(function ($) {
    'use strict';

    var defaults = {
        getUrl : './demo.php',
        submitUrl : './submit.php',
        maxrows : 100,
        templates : {
            ul : '<ul class="byrexcel-errorlist">' + 
                '</ul>',
            li : '<li class="byrexcel-error" error-row="{row}" error-col="{col}">' + '{value}' + 
                '</li>',
            table : '<table class="byrexcel-table">' + 
                '</table>',
            td : '<td class="byrexcel-td" data-row="{row}" data-column="{column}">' + '{value}' + 
                '</td>',
            th : '<th class="byrexcel-th">' + '{value}' + 
                '</th>',
            input :  '<div id="byrexcel-inputdiv" style="display:none;"><input id="byrexcel-input" type="text" style="position:fixed;"/></div>',
            button : '<button id="byrexcel-submit">提交</button>'
        },
        errorMessages : {
            commonError : "{error}",
            elementError : "ROW {row} COL {col} ERROR {value} TIPS : {tip}"
        }
    };

    $.fn.byrexcel = function (options) {
        var root = this;
        this.options = $.extend(defaults, options);
        this.request = {items : Array(), id:''};

        this.init = function (data) {
            console.log(this.html(''));
            this.content = data.content;
            this.header = data.header;
            this.request.id = data.id;
            this.error = data.error;
            this.render();
            this.bindEvents();
        };

        this.render = function () {
            this.append(this.options.templates.ul);
            this.append(this.options.templates.table);
            this.renderTable(this.children("table"));
            this.renderError(this.children("ul"));
            this.append(this.options.templates.button);
            this.append(this.options.templates.input);
        };

        this.renderError = function (errorlist) {
            var i;
            for (i in this.error) {
                if (this.error.hasOwnProperty(i)) {
                    if (this.error[i].type == "column" || this.error[i].type == "special") {
                        errorlist.append(this.options.templates.li.replace('{value}', this.options.errorMessages.commonError).replace('{error}', this.error[i].value));
                    }
                    else if(this.error[i].type == 'element') {
                        errorlist.append(this.options.templates.li.replace('{value}', this.options.errorMessages.elementError).replace('{row}', this.error[i].row).replace('{col}', parseInt(this.error[i].col) + 1).replace('{value}', this.error[i].value).replace('{tip}', this.error[i].tips).replace('{row}', this.error[i].row).replace('{col}', parseInt(this.error[i].col) + 1));
                        this.find('td[data-row=' + this.error[i].row + '][data-column=' + (parseInt(this.error[i].col) + 1) + ']').addClass('byrexcel-error');
                    }
                }
            }
        };

        this.renderTable = function (table) {
            var i;
            var j;
            var td;
            var table_content;

            table_content += '<tr class="byrexcel-tr">';
            for (i in this.header) {
                if (this.header.hasOwnProperty(i)) {
                    table_content += this.options.templates.th.replace('{value}', this.header[i]);
                }
            }
            table_content += '</tr>';

            for (i in this.content) {
                if (this.content.hasOwnProperty(i)) {
                    if (i > this.options.maxrows) {
                        break;
                    }
                    table_content += '<tr>';
                    for (j in this.content[i]) {
                        if (this.content[i].hasOwnProperty(j)) {
                            td = this.options.templates.td.replace('{row}', i).replace('{column}', parseInt(j) + 1).replace('{value}', this.content[i][j]);
                            table_content += td;
                        }
                    }
                    table_content += '</tr>';
                }
            }

            table.append(table_content);
        };


        this.bindEvents = function () {
            this.children('ul').children('li').click(function () {
                root.find('td[data-row=' + $(this).attr("error-row") + '][data-column=' + $(this).attr("error-col") + ']').animate({backgroundColor:'red'}, 400).animate({backgroundColor:'white'}, 400).animate({backgroundColor:'red'}, 400).animate({backgroundColor:'white'}, 400);
            });

            this.children('table').find('.byrexcel-td').click(function (){
                var inputdiv = root.find('#byrexcel-inputdiv');
                var td = $(this);
                if (!$(this).hasClass('byrexcel-error')) {
                    return ;
                }
                inputdiv.show().children().css({width: $(this).width(), height: $(this).height(), left: $(this).offset().left, top: $(this).offset().top}).val($(this).html()).trigger('focus').focusout(function(){
                    var exists = false;
                    var i;
                    td.html($(this).val());
                    for (i in root.request.items) {
                        if(root.request.items.hasOwnProperty(i)) {
                            if(root.request.items[i].row == td.attr('data-row') && root.request.items[i].col == td.attr('data-column')-1){
                                root.request.items[i].value = $(this).val();
                                exists = true;
                            }      
                        }
                    }
                    if(!exists){
                        root.request.items.push({row:td.attr('data-row'), col:td.attr('data-column')-1, value:$(this).val()});
                    }
                    $(this).unbind('focusout');
                    inputdiv.hide();
                });
            });
            this.children('button').click(function() {
                $.post(root.options.submitUrl, {excel_data : root.request}, function(retdata){
                    root.init(retdata);
                },'json');
            });
        }

        return this.each(function () {
            root.addClass('byrexcel');
            $.getJSON(root.options.getUrl, function (retdata) {
                root.init(retdata);
            });
        });
    };
}(jQuery));
