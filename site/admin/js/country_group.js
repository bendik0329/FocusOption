$(function () {
    $('#addCountriesGroup button').on('click', function (e) {
        e.preventDefault();

        var params = {
            action: 'add',
            merchant_id: $('#addCountriesGroup .cpabc_mid').val(),
            name: $('#addCountriesGroup .cpabc_name').val(),
            value: $('#addCountriesGroup .cpabc_value').val(),
            countries: $('#addCountriesGroup .cpabc_countries').val(),
        };

        $.ajax({
            url: "/ajax/CpaCountriesGroup.php",
            data: params,
            type: 'post',
            dataType: "json",
            success: function (result) {

                console.log(result);

                if (result['status'] == 'error') {
                    toastr["warning"](result['message'], 'Countries Group');
                    $("#addCountriesGroup span.result").html('<b style="color:red;">' + result['message'] + '</b>');
                }

                if (result['status'] == 'ok') {
                    toastr["success"]('Countries group has been added', 'Countries Group');
                    $("#addCountriesGroup span.result").html('');
                    $('<tr ref="' + result['data']['id'] + '"><td><input type="text" name="my_cg_name" value="' + result['data']['name'] + '"/></b></td><td>' + result['data']['currency'] + '<input type="text" name="my_cg_value" value="' + result['data']['value'] + '"/></td><td>' + result['data']['countries'] + '</td><td><button class="my_cg_update">Update</button> <button class="my_cg_delete">Delete</button></td></tr>').appendTo('#listCountriesGroup table');
                }


            }
        });

        return false;
    });

    $('#listCountriesGroup button.my_cg_delete').on('click', function (e) {
        e.preventDefault();

        var isConfirm = confirm('Do you want to delete an item?');

        if (!isConfirm) {
            return false;
        }

        var el = $(this).closest('tr');
        var id = el.attr('ref');

        if (id > 0) {
            var params = {
                action: 'delete',
                id: id
            };

            $.ajax({
                url: "/ajax/CpaCountriesGroup.php",
                data: params,
                type: 'post',
                dataType: "json",
                success: function (result) {

                    console.log(result);

                    if (result['status'] == 'error') {
                        toastr["warning"](result['message'], 'Countries Group');
                    }

                    if (result['status'] == 'ok') {
                        toastr["success"]('Countries group has been added', 'Countries Group');
                        el.remove();
                    }


                }
            });

        } else {
            toastr["warning"]('Delete Error', 'Countries Group');
        }

        return false;
    });

    $('#listCountriesGroup button.my_cg_update').on('click', function (e) {
        e.preventDefault();

        var el = $(this).closest('tr');
        var id = el.attr('ref');

        if (id > 0) {
            var params = {
                action: 'update',
                id: id,
                name: el.find("input[name='my_cg_name']").val(),
                value: el.find("input[name='my_cg_value']").val(),
            };

            $.ajax({
                url: "/ajax/CpaCountriesGroup.php",
                data: params,
                type: 'post',
                dataType: "json",
                success: function (result) {

                    console.log(result);

                    if (result['status'] == 'error') {
                        toastr["warning"](result['message'], 'Countries Group');
                    }

                    if (result['status'] == 'ok') {
                        toastr["success"]('Countries group has been updated', 'Countries Group');
                    }


                }
            });
        } else {
            toastr["warning"]('Update Error', 'Countries Group');
        }


        return false;
    });


    var inst = $('[data-remodal-id=modal]').remodal();

    var cpacg_affiliate_merchant_id = '';

    $('#tabs1-regulareDeals .cpacg_update').on('click', function (e) {
        e.preventDefault();

        cpacg_affiliate_merchant_id = $(this).attr('ref');
        var id = cpacg_affiliate_merchant_id;
        if (id >= 0) {
            inst.open();
        } else {
            toastr["warning"]('Group not found', 'CPA by Countries Group');
        }
        return false;
    });

    $(document).on('opening', '#modal-cpacg-list.remodal', function (e) {

        var merchant_id = cpacg_affiliate_merchant_id;
        var affiliate_id = $(this).attr('ref');

        if (merchant_id >= 0 && affiliate_id > 0) {
            var params = {
                action: 'affiliate-groups',
                affiliate_id: affiliate_id,
                merchant_id: merchant_id,
            };

            $.ajax({
                url: "/ajax/CpaCountriesGroup.php",
                data: params,
                type: 'post',
                dataType: "json",
                success: function (result) {

                    console.log(result);

                    if (result['status'] == 'error') {
                        toastr["warning"](result['message'], 'Countries Group');
                        inst.close();
                    }

                    if (result['status'] == 'ok' && result['data']) {

                        $('#modal-cpacg-list .remodal-cancel').show();

                        var currency = result['data']['currency'];
                        var groups = result['data']['groups'];

                        var list = '<table class="table table-striped" style="font-size: 14px;margin: 20px 0 40px;"><tr><th style="font-size: 14px;" width="170">Name</th><th style="font-size: 14px;" width="170">Commision</td><th style="font-size: 14px;margin: 20px 0;">Countries</th><th width="70"></th></rt>';

                        if(groups.length > 0){
                            for (key in groups) {
                                list = list + '<tr ' + ((groups[key]['status'])?' style="background-color: #dfffe1;"':' style="background-color: #ffdfdf;"') + '><td>' + groups[key]['name'] + '</td><td>' + currency + ' <input type="text" value="' + groups[key]['value'] + '"/></td><td>' + groups[key]['countries'] + '</td><td><button ref="' + groups[key]['id'] + '">Update</button></td></tr>';
                            }
                        }else{
                            list = list + '<tr><td colspan="4">No groups for this merchant</td></tr>';    
                        }
                        list = list + '</table>';



                        $('#modal-cpacg-list .cpa-content').html(list);

                    }


                }
            });
        } else {
            toastr["warning"]('Update Error', 'CPA by Countries Group');
        }

    });

    $(document).on('closing', '#modal-cpacg-list.remodal', function (e) {
        $('#modal-cpacg-list .cpa-content').html('<p style="text-align center;"><img src="/images/ajax-loader_big.gif"/></p>');
    });

    $(document).on('click', '#modal-cpacg-list .cpa-content button', function (e) {
        e.preventDefault();
        
        var btn = $(this);
        
        var merchant_id = cpacg_affiliate_merchant_id;
        var affiliate_id = $(this).closest('div#modal-cpacg-list').attr('ref');
        var group_id = $(this).attr('ref');
        var value = $(this).closest('tr').find('input').val();
        
        btn.attr("disabled", true);
        
        if (merchant_id >= 0 && affiliate_id > 0 && group_id > 0) {
            var params = {
                action: 'affiliate-update-cpa-group',
                affiliate_id: affiliate_id,
                merchant_id: merchant_id,
                group_id: group_id,
                value: value,
            };
            
            $.ajax({
                url: "/ajax/CpaCountriesGroup.php",
                data: params,
                type: 'post',
                dataType: "json",
                success: function (result) {

                    console.log(result);

                    if (result['status'] == 'error') {
                        toastr["warning"](result['message'], 'CPA by Countries Group');
                    }

                    if (result['status'] == 'ok') {
                        toastr["success"](result['message'], 'CPA by Countries Group');
                    }

                },
                complete : function(){
                    btn.attr("disabled", false);
                }
            });
            
        }else{
            toastr["warning"]('Update Error', 'CPA by Countries Group');
        }

        return false;
    });

});
