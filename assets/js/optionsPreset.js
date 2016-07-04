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
        showPresetList: false
    },
    // Anything within the ready function will run when the application loads
    ready: function () {
        presetId = $('#presets').data('presetid');

        this.newPreset.preset_id = presetId;
        this.fetchPresets();

    },

    // Methods we want to use in our application are registered here
    methods: {
        fetchPresets: function () {
            this.$http.post('/admin/presetapi/presets', {id: presetId}, function (data, status, request) {
                // set data on vm
                this.$set('presets', data);

            }).then(function () {
                    //this.sort();
                    this.showPreseList = true;
                    $('#presets-list').show();
                    //console.log(this.newPreset)
                },
                function (error) {
                    console.log(error);
                }
            );


        },
        addPreset: function (event) {
            event.preventDefault();
            this.$http.post('/admin/presetapi/create', this.newPreset, function (data, status, request) {
                console.log(data);
                vmPresets.newPreset.id = data.id;
            }).then(
                    function (response) {

                        //  Вставляем в массив значений
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
                this.$http.put('/admin/presetapi/update/' + preset.id, preset)
                    .success(function (response) {
                        this.edited = false;
                        this.editedPreset = null;
                        // this.fetchEvents();
                    }).error(function (error) {
                    console.log(error);
                });
            }
        },
        editPreset: function (preset) {
            console.log(preset);
            this.edited = true;
            this.editedPreset = preset;
        },
        removePreset: function (preset) {
            if (confirm("Вы уверены что хотите удалить этот значение?")) {
                this.$http.delete('/admin/presetapi/delete/' + preset.id, this.block)
                    .then(
                        function (response) {
                            this.presets.$remove(preset);
                            this.sort();
                        }
                        , function (response) {

                        }
                    )
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
                url: '/admin/presetapi/sort'
            });
        }
    });
});