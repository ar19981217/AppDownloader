<template>
    <div class="mt-5 container">
        <table class="table table-bordered">
            <thead>
            <tr>
                <td scope="col" width="10px">Index</td>
                <td scope="col">Name</td>
                <td scope="col">Path</td>
                <td scope="col">Mime</td>
                <td scope="col">Size</td>
                <td scope="col">Status</td>
                <td scope="col" width="10px">Delete</td>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(file, index) in allFiles.data">
                <td scope="row" class="text-center">{{index+1}}</td>
                <td>{{file.title}}</td>
                <td><a :href="/filedownload/+file.path" class="downloadFileLink">{{file.file_name}}</a></td>
                <td>{{file.mime}}</td>
                <td>{{file.size}}</td>
                <td>{{file.status}}</td>
                <td class="text-center">
                    <button @click="deleteDownloadedFile(file.id)" class="border-0 " style="color: #f60"><i
                        class="fas fa-trash"></i></button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        name: "list",
        data(){
          return{
              allFiles: this.files,
          }
        },
        props:[
            'files'
        ],
        mounted() {
            setInterval(() => {
                this.refreshList();
            }, 200)
        },
        methods:{
            refreshList() {
                axios.get('/getListFile')
                    .then(response => {
                        this.allFiles = response.data;
                    }).catch(error => {
                    console.log(error)
                });
            },
            deleteDownloadedFile(id) {
                axios.delete(`/filedelete/${id}`)
                    .then(response => {
                        // this.refreshList();
                    }).catch(error => {
                    console.log(error)
                });
            },
        }
    }
</script>

<style scoped>

</style>
