    var NUM_ROWS_IN_TABLE = 7;

    for(var n=1; n<=NUM_ROWS_IN_TABLE; n++){
        
        var row_has_data = $("#sample_"+n).val();
        
        if(!row_has_data) disableRow(n);
    }
    // enableRow(1);
    // enableRow(2);



    function rowIsEditable(rowNumber){

        var editable;
        var this_user_can_approve = false; /* change this based on currently logged-in user */
        var sample_not_yet_approved = $("#sample_verified_"+rowNumber).val() === "NO" ? false : true;
        
        if(sample_not_yet_approved) {
            editable = true;
        }
        else if (this_user_can_approve){
            editable = true;
        }
        else{
        // sample has been approved, but current user is not allowd to edit after approvals
            editable = false;
        }

        return editable;
    }

    function disableRow(rowNumber) {

        lockRow(rowNumber, true);
    }

    function enableRow(rowNumber) {

        window.alert('rowNumber ' + rowNumber);// + ', rowIsEditable() = ' + rowIsEditable(rowNumber) );

        if(rowIsEditable(rowNumber)) 
            unlockRow(rowNumber);
    }

    function unlockRow (rowNumber) {
        lockRow(rowNumber, false);
    }

    function lockRow(rowNumber, lock_this_row) {

        var row, new_state;

        if( ! isFinite(rowNumber) ) return;// bad input. stop.

        row = 'tr#row_'+rowNumber+'.parent_row :input';
        new_state = lock_this_row ? true : false;

        $(row).prop('disabled', new_state); 
    }

    