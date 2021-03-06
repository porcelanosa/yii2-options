var vmPresets = new Vue({
    el: '#presets',
    data: {
        newPreset: {
            value: '',
            preset_id: 0,
            sort: 0
        },
        presets: Object,
        editedPreset: null,
        edited: false,
        showPresetList: false,
        appUrl: '/backend/'
    },
    // Anything within the ready function will run when the application loads
    ready: function () {
        presetId = $('#presets').data('presetid');
        this.appUrl = document.getElementById('optionForm').getAttribute('data-appUrl');

        this.newPreset.preset_id = presetId;
        this.fetchPresets();

    },

    // Methods we want to use in our application are registered here
    methods: {
        fetchPresets: function () {
            this.$http
                .post(this.appUrl+'/options/presetapi/presets', {id: presetId})
                .then(
                    function (responce) {
                        this.$set('presets', responce.data);
                        this.showPresetList = true;
                        $('#presets-list').show();
                    },
                    function (response) {
                        console.log(response.error);
                        }
                    );

        },
        addPreset: function (event) {
            event.preventDefault();
            this.$http
                .post(this.appUrl+'/options/presetapi/create', this.newPreset)
                .then(
                    function (response) {
                        //  Вставляем в массив значений
                        vmPresets.newPreset.id = response.data.id;
                        this.presets.push(vmPresets.newPreset);
                        this.sort();
                        // обнуляем значения формы
                        vmPresets.newPreset = {
                            value: '',
                            preset_id: presetId,
                            sort: 0
                        };

                        // закрываем блок с формой
                        $('#add-preset-box').addClass('collapsed-box').find('.box-body').hide();
                    },
                    function (error) {
                        console.log(error);
                    });

        },
        savePreset: function (preset) {

            event.preventDefault();
            if (preset.id) {
                var this_ = this;
                $.ajax({
                    data: {id: preset.id, presetdata: preset},
                    type: 'POST',
                    dataType: 'json',
                    url: this.appUrl+'/options/presetapi/updatevalue',
                    success: function (data) {

                        console.log(data.success);
                        if(data.success)
                        {
                            this_.edited = false;
                            this_.editedPreset = null;
                        }
                    }
                });
                /*this.$http
                    .put('/options/presetapi/update/' + preset.id, preset)
                    .then(
                        function (response) {
                            this.edited = false;
                            this.editedPreset = null;
                            // this.fetchEvents();
                        },
                        function (error) {
                            console.log(error);
                    });*/
            }
        },
        editPreset: function (preset) {
            console.log(preset);
            this.edited = true;
            this.editedPreset = preset;
        },
        removePreset: function (preset) {
            if (confirm("Вы уверены что хотите удалить этот значение?")) {
                if (preset.id) {
                    var this_preset = this.presets;
                    $.ajax({
                        data: {id: preset.id},
                        type: 'POST',
                        dataType: 'json',
                        url: this.appUrl+'/options/presetapi/deletevalue',
                        success: function (data) {

                            console.log(data.success);
                            if(data.success)
                            {
                                this_preset.$remove(preset);
                            }
                        }
                    });
                    /*this.$http
                        .delete('/options/presetapi/delete/' + preset.id, this.block)
                        .then(
                            function (response) {
                                this.sort();
                            }
                            , function (response) {

                            }
                        )*/
                }
            }
        },
        sort: function () {
            this.presets.sort(function (a, b) {
                if (a.sort > b.sort) {
                    return 1;
                }
                return 0;
            });
        }
    }
});
/*

 Vue.filter('mounting', function (size) {
 return 1;
 })*/

$(document).ready(function () {
    $("#presets").sortable({
        opacity: 0.6,
        cursor: 'move',
        axis: 'y',
        items: '.preset-block',
        update: function (event, ui) {
            var data = $(this).sortable('serialize');
            /*data1 = $(this).sortable('toArray');
             console.log(data);
             console.log(data1);*/
            // POST to server using $.post or $.ajax
            $.ajax({
                data: data,
                type: 'POST',
                url: this.appUrl+'/options/presetapi/sort'
            });
        }
    });
});