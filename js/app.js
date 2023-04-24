new Vue({
    el: '#app',
    data: {
      message: 'Hello Vue!',
      party: [],
      sort:'pname ASC',
      newParty:{ pname:'',candidate:'',policy:'' },
      editParty:{},
      imageFile:'',
      imagePreview:'',
      oldImageFile:'',
      file:'',
      partyPopularity: 100,
      showModalCreate:false,
      showModalEdit:false,
      sending:false,
      search:'',
      count: 1,
      chartPartyName: ['A','B','C','D'],
      chartPartyScores: [89, 99, 41, 74],
      chartColorData: ['#f73636', '#f78716', '#344cc2', '#FFF']
    },
    mounted() {
      this.getParty();
      this.renderChart();
    },
    methods: {
      renderChart() {
        const canvas = document.getElementById('myChart');

        if (canvas && canvas.getContext) {
          const ctx = canvas.getContext('2d');
          if (this.chart) {
            this.chart.destroy();
          }
          const dataTable = {
            labels: this.chartPartyName, // use array data from Vue.js
            datasets: [{
              data: this.chartPartyScores, // use array data from Vue.js
              backgroundColor: this.chartColorData,
              borderWidth: 0,
            }]
          };
          const options = {
            maintainAspectRatio: true,
            responsive: true,
          };
          this.chart = new Chart(ctx, {
            type: 'doughnut',
            data: dataTable,
            options: options,
          });
        }
      },
      getParty() {
        let _this = this
        _this.loading = true
        axios.get('action.php?action=read&search='+this.search+'&sort='+this.sort)
        .then(function(response){
          if (response.data.code === 200 || response.data.code === '200') {
            _this.party = response.data.data
            _this.chartPartyName = _this.party.map(party => party.pname)
            _this.chartPartyScores = _this.party.map(party => party.scores)
            setTimeout(() => _this.loading = false, 1000)
          }                  
        })
      },
      onToggleModalCreate() {
        this.showModalCreate = !this.showModalCreate
      },
      onClear(){
        setTimeout(() => this.imagePreview = '', 300);
        this.imageFile = ''
        this.oldImageFile = ''
        this.$refs["input_file"].value = "";
        this.newParty.pname = ''
        this.newParty.candidate = ''
        this.newParty.policy = ''
        this.newParty.scores = ''
      },
      handdelFileUpload() {
        this.imageFile = this.$refs.input_file.files[0]
        let reader = new FileReader()
        reader.addEventListener("load", function () {
          this.imagePreview = reader.result;
        }.bind(this), false);
        if(this.imageFile){
          if ( /\.(jpe?g|png|gif)$/i.test( this.imageFile.name ) ) {
          reader.readAsDataURL( this.imageFile );
          }
        }
      },
      toFormData(obj){
        let formData = new FormData()
        for (let key in obj) {
          console.log(obj[key])
          formData.append(key,obj[key])
        }
        console.log(formData)
        if (this.imageFile) {
          formData.append('avatar',this.imageFile)
        }
        return formData
      },
      onCreateParty() {
        console.log('onCreateParty')
        this.sending = true
        let formData = this.toFormData(this.newParty)
        let _this = this
        axios.post('action.php?action=create',formData)
        .then(function(response){
          if (response.data.code===200 || response.data.code==='200') {
            setTimeout(function(){
              _this.showModalCreate = false
              _this.getParty()
              _this.sending = false
              console.log('added')
              Swal.fire({
                icon: 'success',
                title:'The party has been added successfully.',
                text:response.data.message
              })
            },3000)
          }else{
            _this.sending = false
            Swal.fire({
              icon: 'error',
              title:'Please check the details again.',
              text:response.data.message
            })
          }
        })
      },
      onDeleteParty(id) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Confirm'
        }).then((result) => {
          if (result.isConfirmed) {
            this.sending = true
          let formData = this.toFormData({id})
          let _this = this
          axios.post('action.php?action=delete',formData)
          .then(function(response){
            if (response.data.code===200 || response.data.code==='200') {
              _this.getParty()
              _this.sending = false
              Swal.fire({
                icon: 'success',
                title:'Deleted.',
                text:response.data.message
              })
            }else{
              _this.sending = false
              Swal.fire({
                icon: 'error',
                title:'Oops...',
                text:response.data.message
              })
            }
          })
          }
        })
      },
      onUpdateParty() {
        this.sending = true
        const { id,pname,candidate,policy } = this.editParty
        const editParty = { id,pname,candidate,policy }
        let formData = this.toFormData(editParty)
        let _this = this
        axios.post('action.php?action=update',formData)
        .then(function(response){
          if (response.data.code===200 || response.data.code==='200') {
            setTimeout(function(){
              _this.showModalEdit = false
              _this.getParty()
              _this.sending = false
              Swal.fire({
                icon: 'success',
                title:'Updated.',
                text:response.data.message
              })
            },3000)
          }else{
            _this.sending = false
            Swal.fire({
              icon: 'error',
              title:'Oops...',
              text:response.data.message
            })
          }
        })
      },
      onToggleModalEdit(id) {
        this.imagePreview = ''
        this.imageFile = ''
        if (id && id.length > 0) {
          this.showModalEdit = true
          let _this = this
          axios.get('action.php?action=read&id='+id)
          .then(function(response){
            if (response.data.code===200 || response.data.code==='200') {
              _this.editParty = response.data.data
            }
          })
        }else{
          this.showModalEdit = false
        }
      },


    }
  });
  Vue.config.devtools = true
  