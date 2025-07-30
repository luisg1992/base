(function ($) {
    function flatListToTree(items) {
        //console.log(items);
        const getChild = (item, level, allLevel) => {
            //console.log(item);
            //console.log(level);
            //console.log(allLevel);
            return items.filter(v => v.parentId === item.Id)
                .map(v => {
                    const temp = {
                        ...v,
                        level,
                        children: getChild(v, level + 1, level === 0 ? v.Id : `${allLevel}_${v.Id}`),
                        partLevel: level === 0 ? v.Id : `${v.parentId}_${v.Id}`,
                        ...(level === 0 ? {
                            allLevel: v.Id
                        } : {
                            allLevel: [allLevel, v.Id].join('_')
                        }),
                    };
                    return [temp].concat(...temp.children);
                });
        };

        return [].concat(...getChild({ Id: undefined }, 0, undefined));
    }

    function countChildren(node) {
        var sum = 0,
            children = node && node.length ? node : node.children,
            i = children && children.length;

        if (!i) {
            sum = 0;
        } else {
            while (--i >= 0) {
                if (node && node.length) {
                    sum++;
                    countChildren(children[i]);
                } else {
                    sum += countChildren(children[i]);
                }
            }
        }
        return sum;
    }

    function createRows(items) {
        var fragments = document.createDocumentFragment();
        var opts = flatListToTree(items);
        for (var i = 0; i < opts.length; i++) {
            var item = opts[i];
            var trEle = document.createElement('tr');
            $(trEle).attr('data-part-level', item.partLevel);
            $(trEle).attr('data-all-level', item.allLevel);
            $(trEle).attr('data-level', item.level);
            $(trEle).attr('data-count', countChildren(item));
    
            var tdEle1 = document.createElement('td');
            for (var j = 0; j <= item.level; j++) {
                var spanEle = document.createElement('span');
                $(spanEle).addClass('tree-table-space-block');
                $(spanEle).attr('data-part-level', item.partLevel);
                $(spanEle).attr('data-all-level', item.allLevel);
                $(spanEle).attr('data-level', item.level);
                var iEle = document.createElement('i');
                if (j === item.level) {
                    if (item.children && item.children.length > 0) {
                        $(spanEle).addClass('btn-toggle expand');
                        $(spanEle).attr('data-is-open', '1');
                        $(spanEle).attr('data-count', countChildren(item));
                        $(spanEle).text('-');
                    } else {
                        $(spanEle).addClass('last-block');
                        $(spanEle).append(iEle);
                    }
                } else {
                    $(spanEle).append(iEle);
                }
                $(tdEle1).append(spanEle);
            }
    
            var tdElements = [
                item.Codigo,
                item.UnidadOrganica,
                item.TipoUnidadOrganica,
                item.UbicacionFisica,
                item.TipoPuntoCarga,
                item.Estado,
                item.Acciones,
            ];
            
            tdElements.forEach((value, index) => {
                var tdEle = document.createElement('td');
                // Si la columna es "Estado" o "Acciones", usar .html() para renderizar el contenido HTML
                if (index == 5) { // Índices 5 y 6 corresponden a "Estado" y "Acciones"
                    $(tdEle).html(value); // Renderizar contenido como HTML
                } else if (index == 6) {
                    $(tdEle).html(value); // Renderizar contenido como HTML
                    $(tdEle).addClass('dropdown');
                } else if (index == 0){
                    var spanEle2 = document.createElement('span');
                    $(spanEle2).addClass('tree-table-td-content');
                    $(spanEle2).text(value);
                    $(tdEle1).append(spanEle2);
            
                    // Insertar tdEle1 como la primera celda
                    $(trEle).append(tdEle1);
                    return;
                } else {
                    $(tdEle).text(value); // Renderizar contenido como texto
                }
                $(trEle).append(tdEle);
            });

            /*
            var spanEle2 = document.createElement('span');
            $(spanEle2).addClass('tree-table-td-content');
            $(spanEle2).text(item.Codigo);
            $(tdEle1).append(spanEle2);

            var tdEle2 = document.createElement('td');
            $(tdEle2).css('width', '200px');
            var spanTd2 = document.createElement('span');
            $(spanTd2).addClass('tree-table-td-content');
            $(spanTd2).text(item.UnidadOrganica);
            $(tdEle2).append(spanTd2);

            var tdEle3 = document.createElement('td');
            $(tdEle3).css('width', '200px');
            var spanTd3 = document.createElement('span');
            $(spanTd3).addClass('tree-table-td-content');
            $(spanTd3).text(item.TipoUnidadOrganica);
            $(tdEle3).append(spanTd3);

            var tdEle4 = document.createElement('td');
            $(tdEle4).css('width', '200px');
            var spanTd4 = document.createElement('span');
            $(spanTd4).addClass('tree-table-td-content');
            $(spanTd4).text(item.UbicacionFisica);
            $(tdEle4).append(spanTd4);

            var tdEle5 = document.createElement('td');
            $(tdEle5).css('width', '200px');
            var spanTd5 = document.createElement('span');
            $(spanTd5).addClass('tree-table-td-content');
            $(spanTd5).text(item.TipoPuntoCarga);
            $(tdEle5).append(spanTd5);

            var tdEle6 = document.createElement('td');
            $(tdEle6).css('width', '200px');
            var spanTd6 = document.createElement('span');
            $(spanTd6).addClass('tree-table-td-content');
            $(spanTd6).html(item.Estado);
            $(tdEle6).append(spanTd6);

            var tdEle7 = document.createElement('td');
            $(tdEle7).css('width', '200px');
            var spanTd7 = document.createElement('span');
            $(spanTd7).addClass('tree-table-td-content');
            $(spanTd7).html(item.Acciones);
            $(tdEle7).append(spanTd7);

            $(trEle).append(tdEle1);
            $(trEle).append(tdEle2);
            $(trEle).append(tdEle3);
            $(trEle).append(tdEle4);
            $(trEle).append(tdEle5);
            $(trEle).append(tdEle6);
            $(trEle).append(tdEle7);
            */
    
            $(fragments).append(trEle);
        }
        $('#table-tree').append(fragments);
    }

    // Convertirlo en un plugin de jQuery
    $.fn.treeTable = function (options) {
        // Verificar si se pasó un conjunto de datos
        var items = options.data || [];
            createRows(items);

        // Delegar eventos de expansión en los elementos de la tabla
        // Remueve cualquier evento previo antes de agregar uno nuevo
        // Función de expansión de niveles
        $(this).off('click', '.expand').on('click', '.expand', function (e) {
            var level = $(this).attr('data-level');
            var partLevel = $(this).attr('data-part-level');
            var allLevel = $(this).attr('data-all-level');
            var isOpen = $(this).attr('data-is-open');
            var trsDiv = $('.tree-table').find('tbody tr');
            var trsArray = $(trsDiv);
            if (isOpen === '1') {
                for(var i = 0;i < trsArray.length - 1; i++) {
                    var tempTr = $(trsArray[i]);
                    var trLevel = tempTr.attr('data-level');
                    var trPartLevel = tempTr.attr('data-part-level');
                    var trAllLevel = tempTr.attr('data-all-level');
                    var contain = trAllLevel.split('_')[Number(level)];
                    var curr = partLevel.split('_');
                    if (contain && contain === curr[curr.length - 1] && partLevel !== trPartLevel) {
                        tempTr.removeClass('show');
                        tempTr.addClass('hidden');
                    }
                }
                $(this).text('+');
                $(this).attr('data-is-open', '0');
            } else {
                for(var i = 0;i < trsArray.length - 1; i++) {
                    var tempTr = $(trsArray[i]);
                    var trLevel = tempTr.attr('data-level');
                    var trPartLevel = tempTr.attr('data-part-level');
                    var trAllLevel = tempTr.attr('data-all-level');
                    var contain = trAllLevel.split('_')[Number(level)];
                    var curr = partLevel.split('_');
                    if (contain && contain === curr[curr.length - 1] && Number(trLevel) > (Number(level))) {
                        var span = $(tempTr.children()[0].children[Number(trLevel)]);
                        var isOpen = $(span).attr('data-is-open');
                        var childrenCount = $(span).attr('data-count');
                        tempTr.removeClass('hidden');
                        tempTr.addClass('show');
                        if (isOpen && isOpen === '0' && Number(childrenCount) > 0) {
                            i = i + Number(childrenCount);
                        } else {
                        if (isOpen === '1') {
                            $(span).attr('data-is-open', '1');
                            $(span).text('-');
                            tempTr.removeClass('hidden');
                            tempTr.addClass('show');
                        }
                        }
                    }
                }
                $(this).text('-');
                $(this).attr('data-is-open', '1');
            }
        });

        return this;
    };

    var isResizing = false;
    var lastX = 0;
    var thElement = null;

    $('th').mousedown(function(e) {
        isResizing = true;
        thElement = $(this);
        lastX = e.pageX;
    });

    $(document).mousemove(function(e) {
        if (isResizing && thElement) {
        var delta = e.pageX - lastX;
        var newWidth = thElement.width() + delta;
        thElement.width(newWidth); // Ajustar el tamaño de la columna
        lastX = e.pageX;
        }
    });

    $(document).mouseup(function() {
        isResizing = false;
        thElement = null;
    });

})(jQuery);
