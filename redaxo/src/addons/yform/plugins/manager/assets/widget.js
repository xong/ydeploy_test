
function openYFormDataset(id, field, link)
{
    var newWindowLink = link + '&rex_yform_manager_opener[id]='+id+'&rex_yform_manager_opener[field]='+field+'&rex_yform_manager_opener[multiple]=0';
    return newWindow( id, newWindowLink, 1200,800,',status=yes,resizable=yes');
}

function openYFormDatasetList(id, field, link)
{
    var newWindowLink = link + '&rex_yform_manager_opener[id]='+id+'&rex_yform_manager_opener[field]='+field+'&rex_yform_manager_opener[multiple]=1';
    return newWindow( id, newWindowLink, 1200,800,',status=yes,resizable=yes');
}

function deleteYFormDataset(id){
    var a;
    a = new getObj('YFORM_DATASET_FIELD_' + id);
    a.obj.value = '';
    a = new getObj('YFORM_DATASET_SELECT_' + id);
    a.obj.value = '';
}

function deleteYFormDatasetList(id){
    deleteREX(id, 'YFORM_DATASETLIST_FIELD_', 'YFORM_DATASETLIST_SELECT_');
}

function moveYFormDatasetList(id, direction){
    moveREX(id, 'YFORM_DATASETLIST_FIELD_', 'YFORM_DATASETLIST_SELECT_', direction);
}

function writeYFormDatasetlist(id){
    writeREX(id, 'YFORM_DATASETLIST_FIELD_', 'YFORM_DATASETLIST_SELECT_');
}

function setYFormDataset(id, data_id, data_name, multiple){

    var event = opener.jQuery.Event("rex:YForm_selectData");
    opener.jQuery(window).trigger(event, [data_id, data_name, multiple]);
    if (event.isDefaultPrevented()) {
        self.close();
    }

    if(multiple == 1) {
        var datalist = "YFORM_DATASETLIST_SELECT_"+id;
        var source = opener.document.getElementById(datalist);
        var sourcelength = source.options.length;

        option = opener.document.createElement("OPTION");
        option.text = data_name;
        option.value = data_id;

        source.options.add(option, sourcelength);
        opener.writeREX(id, 'YFORM_DATASETLIST_FIELD_', 'YFORM_DATASETLIST_SELECT_');

    }else {
        var data_field_name = 'YFORM_DATASET_SELECT_' + id;
        var data_field_id = 'YFORM_DATASET_FIELD_' + id;
        opener.document.getElementById(data_field_name).value = data_name;
        opener.document.getElementById(data_field_id).value = data_id;
        self.close();
    }

}
