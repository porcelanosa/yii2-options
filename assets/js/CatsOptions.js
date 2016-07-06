var vmOptionsCatsList = new Vue({
    el: '#CatsOptionsMain',
    data: {
        newOption: {
            option_id: 0,
            name: 0,
            sort: 0
        },
        
        Options: null,
        ParentOptions: null,
        CurrentOptions: null,
        editedOptions: null,
        edited: false,
        currentCatId: false,
        model_name: null,
        classSelectedCatObject: {
            'selected-cat': true
        }
    },
    // Anything within the ready function will run when the application loads
    ready: function () {
        this.model_name = document.getElementById('CatsOptionsMain').getAttribute('data-modelName');

        /*this.newPreset.preset_id = presetId;*/
         this.fetchOptions();

    },

    // Methods we want to use in our application are registered here
    methods: {
        fetchOptions: function () {
            this.$http.post('/options/catsoptions/get-options-for-child', {model_name: 'Cats-Items'}, function (data, status, request) {
                // set data on vm
                this.$set('Options', data);

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
        selectCat: function (event) {
            $('#cat-list li').removeClass('selected-cat');
            var id = event.target.getAttribute('data-id');
            $('#cat-list [data-id = '+id+']').addClass('selected-cat');

            if(sortableOptions.options["group"].pull == false) {
                sortableOptions.options["group"].pull = "clone"
            }
            this.currentCatId = id;
            this.$http.post('/options/catsoptions/get-options-by-cat-id', {model_id: id}, function (data, status, request) {
                this.$set('CurrentOptions', data);
            }).then(function () {
                },
                function (error) {
                    console.log(error);
                }
            );
            this.$http.post('/options/catsoptions/get-all-parent-options', {model_id: id}, function (data, status, request) {
                this.$set('ParentOptions', data);
            }).then(function () {
                },
                function (error) {
                    console.log(error);
                }
            );
        },

        saveCatsOptionsList: function (optionsArray) {
            event.preventDefault();
            if (optionsArray.length > 0) {
                this.$http.post(
                    '/options/catsoptions/update',
                    {
                        cat_id: this.currentCatId,
                        options: optionsArray
                    },function (data, status, request) {
                        //  Вставляем в массив Options
                        this.CurrentOptions.push({option_id: parseInt(data.option_id)});
                    })
                    .then(function (response) {
                    },
                    function (error) {

                    });
            }
        },
        removeCurrOption: function (item) {


            if (confirm("Вы уверены что хотите удалить этот значение?")) {
                this.$http.post(
                    '/options/catsoptions/delete-option',
                    {
                        option_id: item.option_id,
                        model_id: this.currentCatId
                    },
                    function (data, status, request) {
                    })
                    .then(
                        function (response) {
                            this.CurrentOptions.$remove(item);
                            //this.sort();
                        }
                        , function (response) {

                        }
                    )
            }
        },
        optionName: function (id) {
            /*Array.prototype.filterObjects = function(key, value) {
                return this.filter(function(x) { return x[key] === value; })
            }*/
            result = this.Options.filter(function(v) {
                return v.id === id; // Filter out the appropriate one
            })[0].name; // Get result and access the foo property
            //this.Options.find(x=> x.id === id).name
            return result
        }
    }

});

var vmOptions = new Vue({
    el: '#options-list',
    data: {
        newOption: {
            option_id: 0,
            name_id: 0,
            sort: 0
        },
        options: Object,
        editedPreset: null,
        edited: false,
        currentCat: false
    },
    // Anything within the ready function will run when the application loads
    ready: function () {
        model_name = $('#CatsOptionsMain').data('modelName');

        /*this.newPreset.preset_id = presetId;
         this.fetchPresets();*/

    },

    // Methods we want to use in our application are registered here
    methods: {
        fetchPresets: function () {
            this.$http.post('/options/presetapi/presets', {id: presetId}, function (data, status, request) {
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
            this.$http.post('/options/presetapi/create', this.newPreset, function (data, status, request) {
                console.log(data);
                vmOptionsCatsList.newPreset.id = data.id;
            }).then(
                function (response) {

                    //  Вставляем в массив размеров
                    this.presets.push(vmOptionsCatsList.newPreset);
                    this.sort();
                    // обнуляем значения формы
                    vmOptionsCatsList.newPreset = {
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
        saveCatsOptionsList: function (catsArray, optionsArray) {
            event.preventDefault();
            if (catsArray.length > 0 && optionsArray.length > 0) {
                var arr = [catsArray, optionsArray];
                var catsObj = catsArray.reduce(function (o, v, i) {
                    o[i] = v;
                    return o;
                }, {});
                var optionsObj = optionsArray.reduce(function (o, v, i) {
                    o[i] = v;
                    return o;
                }, {});
                console.log(arr);

                this.$http.post(
                    '/options/childoptionslist/update',
                    {
                        //arr: arr,
                        cats: catsArray,
                        options: optionsArray
                    },
                    function (data, status, request) {
                        /*console.log(data);
                         vmOptionsCatsList.newPreset.id = data.id;*/
                    }).then(function (response) {

                    },
                    function (error) {

                    });
                /*this.$http.put('/options/childoptionslist/update/' + catsArray.concat(optionsArray))
                 .success(function (response) {
                 this.edited = false;
                 this.editedPreset = null;
                 // this.fetchEvents();
                 }).error(function (error) {
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
                this.$http.delete('/options/presetapi/delete/' + preset.id, this.block)
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