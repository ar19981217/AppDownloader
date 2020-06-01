<template>
    <div class="container mt-5">
        <div class="row justify-content-center my-3 my-card" :rel="index" v-for="(download, index ) in downloads">
            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" :rel="index" class="form-control  file-input" placeholder="Url file..."
                           v-model="download.file">
                    <span class="invalid-feedback" role="alert">
                        <strong>Please enter a valid URL.</strong>
                    </span>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group d-flex align-items-start">
                    <template v-if="download.is_new">
                        <div class="form-group w-100">
                            <input type="text" :rel="index" class="form-control title-input" placeholder="Name..."
                                   v-model="download.title">
                            <span class="invalid-feedback" role="alert">
                            <strong>This field is required.</strong>
                        </span>
                        </div>
                        <button class="btn btn-outline-primary ml-2 d-flex align-items-center upload"
                                @click="uploadFile(download, index)" type="submit" :rel="index">
                            <i class="fa fa-spin fa-spinner mr-2" style="display: none"></i>
                            Upload
                        </button>
                    </template>
                    <button class="btn btn-outline-danger ml-2" @click="deleteFile(index)" type="submit">
                        Delete
                    </button>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <button class="btn btn-outline-primary ml-2" @click="addFile()" type="submit">AddFile Url</button>
        </div>
        <template>
            <div class="mt-5">
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
                    <tr v-for="(file, index) in allFiles.data" :rel="file.id" class="list">
                        <td scope="row" class="text-center">{{index+1}}</td>
                        <td>{{file.title}}</td>
                        <td><a :href="/filedownload/+file.file_name" class="downloadFileLink">{{file.file_name}}</a></td>
                        <td>{{file.mime}}</td>
                        <td>{{file.size}}</td>
                        <td>{{file.status}}</td>
                        <td class="text-center">
                            <button @click="deleteDownloadedFile(file.id)" class="border-0 dl-btn" style="color: #f60">
                                <i
                                    class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        name: "Form",
        props: [
            'files'
        ],
        data() {
            return {
                allFiles: this.files,
                url: null,
                name: null,
                downloads: [],
                fileProgress: 0,
                fileCurrent: '',
            }
        },
        mounted() {
            setInterval(() => {
                this.refreshList();
            }, 200)
        },
        methods: {
            addFile() {
                this.downloads.push({id: 0, title: '', file: [], is_new: true})
            },
            deleteFile(index) {
                if (this.downloads[index].is_new) {
                    let inputVal = document.querySelectorAll('.my-card');
                    inputVal.forEach((item) => {
                        if (item.getAttribute('rel') == index) item.remove();
                    });
                    return;
                }
                axios.delete('/download/' + this.downloads[index].id,)
                    .then(response => {
                        if (response.data) {
                            this.downloads.splice(index, 1)
                        }
                    })
            },
            validURL(str) {
                var pattern = new RegExp('^(https?:\\/\\/)?' +
                    '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' +
                    '((\\d{1,3}\\.){3}\\d{1,3}))' +
                    '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' +
                    '(\\?[;&a-z\\d%_.~+=-]*)?' +
                    '(\\#[-a-z\\d_]*)?$', 'i');
                return !!pattern.test(str);
            },

            async uploadFile(download, index) {
                if (this.valid(download.title, index, this.validURL(download.file))) {
                    let inputVal = document.querySelectorAll('.upload');
                    inputVal.forEach((item) => {
                        if (item.getAttribute('rel') == index) {
                            item.setAttribute("disabled", "disabled");
                            item.querySelector('.fa-spinner').style.display = 'block';
                        }
                    });
                    let form = new FormData();
                    form.append('file', download.file);
                    form.append('name', download.title);
                    await axios.post('/fileadd', form).then(response => {
                        form.append('id', response.data);
                        if (response.data) {
                            this.refreshList();
                            axios.post('/download', form).then(response => {
                                if (response.data) {
                                    this.refreshList();
                                    download.id = response.data;
                                    download.is_new = false;
                                    let inputVal = document.querySelectorAll('.list');
                                    inputVal.forEach((item) => {
                                        if (response.data == item.getAttribute('rel')) {
                                            item.querySelector('.dl-btn').setAttribute('disabled', 'disabled');
                                            item.querySelector('.dl-btn').style.opacity = '.5';
                                        }
                                    });
                                }
                            }).catch(error => {
                                console.log(error)
                            });
                        }

                    }).catch(error => {
                        console.log(error)
                    });
                    this.fileProgress = 0;
                    this.fileCurrent = '';
                }
            },

            refreshList() {
                axios.get('/getListFile')
                    .then(response => {
                        this.allFiles = response.data;
                    }).catch(error => {
                    console.log(error)
                });
            },
            async deleteDownloadedFile(id) {
                await axios.delete(`/filedelete/${id}`)
                    .then(response => {
                    }).catch(error => {
                        console.log(error)
                    });
            },
            valid(title, index, validUrl) {
                if (title && validUrl) {
                    let inputVal = document.querySelectorAll('.file-input');
                    inputVal.forEach((item) => {
                        if (item.getAttribute('rel') == index) {
                            item.classList.remove('is-invalid');
                        }
                    });
                    return true;
                }
                if (!validUrl) {
                    let inputVal = document.querySelectorAll('.file-input');
                    inputVal.forEach((item) => {
                        if (item.getAttribute('rel') == index) {
                            item.classList.add('is-invalid');
                        }
                    });
                } else {
                    let inputVal = document.querySelectorAll('.file-input');
                    inputVal.forEach((item) => {
                        if (item.getAttribute('rel') == index) {
                            item.classList.remove('is-invalid');
                        }
                    });
                }
                if (title == '') {
                    let inputVal = document.querySelectorAll('.title-input');
                    inputVal.forEach((item) => {
                        if (item.getAttribute('rel') == index) item.classList.add('is-invalid');
                    });

                } else {
                    let inputVal = document.querySelectorAll('.title-input');
                    inputVal.forEach((item) => {
                        if (item.getAttribute('rel') == index) item.classList.remove('is-invalid');
                    });
                }
            },
        }
    }


</script>

<style scoped>

</style>
