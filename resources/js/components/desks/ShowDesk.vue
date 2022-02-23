<template>
    <div class="container">
        <h1>{{ name }}</h1>
        <div class="form-group">
            <input type="text" @blur="saveName" v-model="name" class="form-control">
        </div>
        <br>

        <form @submit.prevent="addNewDeskList">
            <div class="form-group">
                <input type="text" v-model="desk_list_name" class="form-control" placeholder="Введите название списка">
            </div>
            <button type="submit" class="btn btn-primary">добавить список</button>
        </form>
        <br>


        <div class="row">
            <div class="col-lg-4" v-for="desk_list in desk_lists">
                <div class="card mt-3">
                    <div class="card-body">
                        <form @submit.prevent="updateDeskList(desk_list.id, desk_list.name)"
                              class="d-flex justify-content-between align-items-center"
                              v-if="desk_list_input_id == desk_list.id">

                            <input type="text" v-model="desk_list.name" class="form-control"
                                   placeholder="Введите название списка">
                            <button type="button" @click="desk_list_input_id = null" class="close" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </form>
                        <h5 v-else class="card-title d-flex justify-content-between align-items-center"
                            style="cursor: pointer" @click="desk_list_input_id = desk_list.id">{{ desk_list.name }} <i
                            class="fas fa-pencil-alt">@</i></h5>
                        <button type="submit" class="btn btn-danger  mt-3" @click="deleteDeskList(desk_list.id)">Удалить
                        </button>
                        <div class="card mt-3 bg-light" v-for="card in desk_list.cards" :key="card.id">
                            <div class="card-body">
                                <h4 class="card-title d-flex justify-content-between align-items-center mb-3">
                                    {{ card.name }}</h4>
                                <!-- Button trigger modal -->
                                <button type="button" @click="getCard(card.id)" class="btn btn-primary mt-3"
                                        data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    открыть
                                </button>

                                <button type="button" class="btn btn-secondary  mt-3" @click="deleteCard(card.id)">
                                    Удалить
                                </button>
                            </div>
                        </div>
                        <form @submit.prevent="addNewCard(desk_list.id)" class=" mt-3">
                            <input type="text" v-model="card_names[desk_list.id]" class="form-control"
                                   placeholder="введите название карточки">
                        </form>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <form @submit.prevent="updateCardName" v-if="show_card_name_input"
                                              class="d-flex justify-content-between align-items-center">
                                            <input type="text" v-model="current_card.name" class="form-control"
                                                   placeholder="Введите название карточки">
                                            <button type="button" class="close" @click="show_card_name_input = false"
                                                    aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </form>
                                        <h5 class="modal-title" id="exampleModalLabel" v-if="!show_card_name_input"
                                            @click="show_card_name_input = true">{{ current_card.name }}<i
                                            class="fas fa-pencil-alt m-lg-3">@</i></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-check" v-for="(task, index) in current_card.tasks">
                                            <input class="form-check-input" type="checkbox"
                                                   @change="updateTask(current_card.tasks[index])"
                                                   :id="'inlineCheckbox'+index"
                                                   v-model="current_card.tasks[index].is_done">
                                            <form @submit.prevent="updateTask(current_card.tasks[index])"
                                                  v-if="task_input_name_id == task.id">
                                                <input type="text" v-model="current_card.tasks[index].name"
                                                       class="form-control"
                                                       placeholder="Введите название задачи">

                                            </form>
                                            <label class="form-check-label" v-else
                                                   :for="'inlineCheckbox'+index">{{ task.name }}
                                                <span @click="task_input_name_id = task.id"
                                                      v-if="task_input_name_id != task.id"><i
                                                    class="fas fa-pencil-alt m-lg-3">@</i></span>
                                            </label>
                                            <button type="button" @click="deleteTask(task.id)" class="close ml-3"
                                                    aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form @submit.prevent="addNewTask" class="mt-3">
                                            <div class="form-group">
                                                <input type="text" v-model="new_task_name"
                                                       placeholder="Введите название задачи">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    props: [
        'deskId'
    ],

    data() {
        return {
            name: null,
            desk_list_name: null,
            errored: false,
            loading: true,
            desk_lists: [],
            desk_list_input_id: null,
            card_names: [],
            current_card: [],
            show_card_name_input: false,
            new_task_name: '',
            task_input_name_id: null,
        }
    },
    methods: {
        updateTask(task) {
            axios.patch('/api/v1/tasks/' + task.id, {
                name: task.name,
                is_done: task.is_done,
                card_id: task.card_id,
            })
                .then(response => {
                    this.task_input_name_id = null
                })
                .catch(error => {
                    console.log(error.response)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        deleteTask(id) {
            axios.delete('/api/v1/tasks/' + id)
                .then(response => {
                    this.getCard(this.current_card.id)
                })
                .catch(error => {
                    console.log(error.response)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        addNewTask() {
            axios.post('/api/v1/tasks', {
                name: this.new_task_name,
                card_id: this.current_card.id,
            })
                .then(response => {
                    this.new_task_name = ''
                    this.getCard(this.current_card.id)
                })
                .catch(error => {
                    console.log(error.response)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        updateCardName() {
            axios.patch('/api/v1/cards/' + this.current_card.id, {
                name: this.current_card.name,
                desk_list_id: this.current_card.desk_list_id,
            })
                .then(response => {
                    this.show_card_name_input = false
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        getCard(id) {
            axios.get('/api/v1/cards/' + id)
                .then(response => {
                    this.current_card = response.data.data
                    //  console.log(this.current_card)

                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        deleteCard(id) {
            axios.delete('/api/v1/cards/' + id)
                .then(response => {
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        addNewCard(desk_list_id) {
            axios.post('/api/v1/cards', {
                name: this.card_names[desk_list_id],
                desk_list_id
            })
                .then(response => {
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        updateDeskList(id, name) {
            axios.put('/api/v1/desk-lists/' + id, {
                name,
                desk_id: this.deskId
            })
                .then(response => {
                    this.desk_list_input_id = null
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        getDeskLists() {
            axios.get('/api/v1/desk-lists', {
                params: {
                    desk_id: this.deskId
                }
            })
                .then(response => {
                    this.desk_lists = response.data.data
                    this.desk_lists.forEach(el => {
                        this.card_names[el.id] = ''
                    })
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        saveName() {
            axios.put('/api/v1/desks/' + this.deskId, {
                name: this.name
            })
                .then(response => {
                    this.name = response.data.data.name
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
        addNewDeskList() {
            axios.post('/api/v1/desk-lists', {
                name: this.desk_list_name,
                desk_id: this.deskId,
            })
                .then(response => {
                    this.desk_list_name = ''
                    this.desk_lists = []
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        deleteDeskList(id) {
            axios.delete('/api/v1/desk-lists/' + id)
                .then(response => {
                    this.desk_lists = []
                    this.getDeskLists()
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
    mounted() {
        axios.get('/api/v1/desks/' + this.deskId)
            .then(response => {
                this.name = response.data.data.name
                // console.log(response)
            })
            .catch(error => {
                console.log(error)
                this.errored = true
            })
            .finally(() => {
                this.loading = false
            })
        this.getDeskLists()
    }
}
</script>


