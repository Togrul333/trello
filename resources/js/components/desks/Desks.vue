<template>
    <div class="container">
        <h1>Доски</h1>
        <form @submit.prevent="addNewDesk">
            <div class="form-group">
                <input type="text" v-model="name" class="form-control" placeholder="Введите название доски">
            </div>
            <button type="submit" class="btn btn-primary">добавить</button>
        </form>
        <div class="row">
            <div class="col-lg-4" v-for="desk in desks">
                <div class="card mt-3">
                    <router-link class="card-body" :to="{name: 'ShowDesk',params:{deskId:desk.id}}">
                        <h5 class="card-title">{{ desk.name }}</h5>
                    </router-link>
                    <button type="submit" class="btn btn-danger  mt-3" @click="deleteDesk(desk.id)">Удалить</button>
                </div>
            </div>
        </div>
        <div class="alert alert-danger" role="alert" v-if="errored">
            Ошибка загрузки данных!!
        </div>
        <div class="d-flex justify-content-center" v-if="loading">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            desks: [],
            errored: false,
            errors:[],
            loading: true,
            name:null
        }
    },
    mounted() {
        this.getAllDesks()
    },
    methods: {
        getAllDesks() {
            axios.get('/api/v1/desks')
                .then(response => {
                    this.desks = response.data.data
                    // console.log(response)
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        deleteDesk(id) {
            if (confirm('Вы действително хотите удалить доску?')) {
                axios.delete('/api/v1/desks/' + id)
                    .then(response => {
                        this.desks = []
                        this.getAllDesks()
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
                    .finally(() => {
                        this.loading = false
                    })
            }
        },
        addNewDesk(){
            axios.post('/api/v1/desks',{
                name:this.name,
            })
                .then(response => {
                    this.name = ''
                    this.desks = []
                    this.getAllDesks()
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        }
    }
}
</script>

