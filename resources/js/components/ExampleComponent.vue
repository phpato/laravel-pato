<style scoped>
    .card { background-color: rgba(230, 230, 230, 0.918)}
    .card-header, .card-footer { opacity: 0.1}
</style>

<template>

    <div class="container">
        <section class="row ">
            <div class="col-12">
                <h3 class="mb-4">Row Title</h3>
            </div>
            <div class="col-lg-6 mb-4 d-flex align-items-stretch">
                <div class="card">

                    <div class="card-body">
                        <form >

                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label class="text-primary">Email:</label>
                                            <input type="text" v-model="cita.email" class="form-control" :class="claseEstado($v.cita.email.$error)" 
                                             placeholder="Ingrese email" name="email">
                                        </div>
                                        <div class="text-danger" v-if="submitted&&!$v.cita.email.errors">
                                            <ul>
                                                <li v-if="!$v.cita.email.required">Debe ingresar un email</li>
                                                <li v-if="!$v.cita.email.email">Debe ingresar un email válido</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label class="text-primary">Fecha Cita:</label>
                                            <datepicker :disabled-dates="disabledDates" format="dd-MM-yyyy"  :language="es"  v-model="cita.startDate" :bootstrap-styling="true"></datepicker>
                                        </div>
                                        <div class="text-danger" v-if="submitted&&!$v.cita.email.errors">
                                            <ul>
                                                <li v-if="!$v.cita.startDate.required">Debe ingresar una fecha para la cita</li>
                                            </ul>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>

                            <div class="form-row">
                                 <div class="col" v-if="cita.startDate">

                                    <div class="form-group">
                                        <label class="text-primary">Horas Disponibles:</label>
                                        <v-select v-model="cita.hourDate" :options="horas" label="label" />
 
                                    </div>
                                    <div class="text-danger" v-if="submitted&&!$v.cita.hourDate.errors">
                                        <ul>
                                            <li v-if="!$v.cita.hourDate.required">Debe ingresar una fecha para la cita</li>
                                        </ul>
                                    </div>
                                    
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-primary pull-right" @click="formAddCita">
                                <i class="fa fa-save"> 
                                </i>
                                Guardar
                            </button>
                            
                        </form>

                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4 d-flex align-items-stretch">
                <div class="card  w-100 text-white bg-success">
                    <div class="card-header">Cita pleaneadas</div>
                    <div class="card-body">
                        <vue-good-table
                            :columns="columns"
                            :rows="rows"
                        />
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>

    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import moment from 'moment';
    import 'vue-good-table/dist/vue-good-table.css'
    import { VueGoodTable } from 'vue-good-table';
    import axios from 'axios';
    import {
        required,
        email,
        minLength,    
        sameAs
    } from "vuelidate/lib/validators";

    export default {
        mounted() { 
            console.log('Component mounted.')
        },
        validations: {
            cita: {
                email: {
                    required,
                    email
                },
                startDate:{
                    required
                },
                hourDate:{
                    required
                }
            }
        },
        data () {
            return {
                horas:[
                    {hora: 9, label: "9:00" },
                    {hora: 10, label: "10:00" },
                    {hora: 11, label: "11:00" },
                    {hora: 12, label: "12:00" },
                    {hora: 13, label: "13:00" },
                    {hora: 14, label: "14:00" },
                    {hora: 15, label: "15:00" },
                    {hora: 16, label: "16:00" },
                    {hora: 17, label: "17:00" },
                    {hora: 18, label: "18:00" }
                ],
                disabledDates: {
                    to: new Date(), //antes de esta fecha no se puede elegir
                    from: new Date(2021,1,1), // desabilitar hasta esta fecha
                    days: [6, 0], // descargar fin de semana
                },
                es: es,
                submitted: false,
                cita:{
                    startDate: '',
                    email : '',
                    hourDate: ''
                },
                columns: [
                    {
                        label: 'Name',
                        field: 'name',
                    },
                    {
                        label: 'Age',
                        field: 'age',
                        type: 'number',
                    },
                    {
                        label: 'Created On',
                        field: 'createdAt',
                        type: 'date',
                        dateInputFormat: 'yyyy-MM-dd',
                        dateOutputFormat: 'MMM Do yy',
                    },
                    {
                        label: 'Percent',
                        field: 'score',
                        type: 'percentage',
                    }
                ],
                rows: 
                    [
                        { id:1, name:"John", age: 20, createdAt: '2011-10-31',score: 0.03343 },
                        { id:2, name:"Jane", age: 24, createdAt: '2011-10-31', score: 0.03343 },
                        { id:3, name:"Susan", age: 16, createdAt: '2011-10-30', score: 0.03343 },
                        { id:4, name:"Chris", age: 55, createdAt: '2011-10-11', score: 0.03343 },
                        { id:5, name:"Dan", age: 40, createdAt: '2011-10-21', score: 0.03343 },
                        { id:6, name:"John", age: 20, createdAt: '2011-10-31', score: 0.03343 },
                    ],
                }
        },
        methods: {
            claseEstado(estado){    

                if(this.submitted){

                    if(!estado){

                        return "is-valid";

                    }else{

                        return "is-invalid";

                    }

                }else{

                    return "";

                }


            },
            formAddCita(e) {
      
                this.submitted = true;

                this.$v.$touch();
                if (this.$v.$invalid) {
                    console.log("formulario invalido");
                    return;
                }
                let formData = {
                    start_date: moment(this.cita.startDate).format("YYYY-MM-DD"),
                    hour_date: this.cita.hourDate.hora,
                    email: this.cita.email
                } 
                console.log("el requerimiento antes de enviarlo es: ", formData);
                axios.post(`/cita`, {
                    formData
                })
                .then(response => {

                    console.log("response: ", response);

                })
                .catch(e => {

                    console.log("error: ",e);
                  
                });
            }  
        },
        components:{
            Datepicker,
            VueGoodTable
        }
    }

</script>
