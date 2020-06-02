<style scoped>
    .card { background-color: rgba(230, 230, 230, 0.918)}
    .card-header, .card-footer { opacity: 0.1}
    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
        transition: opacity .3s ease;
        overflow-y: auto;
    }
    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }
</style>

<template>

    <div class="container-fluid">
        
        <section >
            
            <div class="row">
                <div v-if="showCita">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            
                                            <label class="text-primary">Cita de {{cita.email}}</label>
                                         
                                            <button type="button" class="close" @click="cerrarModal">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="form-group">
                                                <label>Email: <b>{{cita.email}}</b></label>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Fecha de la Cita: <b>{{cita.startDate}}</b></label>
                                            </div>

                                            <div class="form-group">
                                                <label>Hora: <b>{{cita.hourDate}}</b></label>
                                            </div>

                                            <div class="form-group">
                                                <label>Fecha Creación: <b>{{cita.creacion}}</b></label>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-danger" @click="cerrarModal">
                                                <i class="fa fa-times"></i>
                                                Cancelar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6 col-lg-6 col-xl-6">
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
                                                <datepicker :disabled="consultar? true : false" placeholder="Seleccione una fecha" :disabled-dates="disabledDates" format="dd-MM-yyyy"  :language="es"  v-model="cita.startDate" :bootstrap-styling="true"></datepicker>
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
                                    <div class="col" v-if="disponibles">

                                        <div class="form-group">
                                            <label class="text-primary">Horas Disponibles:</label>
                                            <v-select v-model="cita.hourDate" :options="horas" label="label">
                                                <span slot="no-options" @click="$refs.select.open = false">
                                                    Sin opciones de hora
                                                </span>
                                            </v-select>
                                        </div>

                                        <div class="text-danger" v-if="consultar">
                                            <ul>
                                                <li>Debe ingresar una hora para la cita</li>
                                            </ul>
                                        </div>

                                    </div>

                                    <div class="col alert alert-danger animate__animated animate__rubberBand" v-if="!disponibles&&consultar">
                                        <i class="fa fa-times">
                                        </i>
                                        No se encontraron horas disponibles para la fecha indicada
                                        <div class="form-group text-center">
                                            <button class="btn btn-danger" @click="reintentar">
                                                <i class="fa fa-check">
                                                </i>
                                                Reintentar
                                            </button>
                                        
                                        </div>
                                    </div>

                                </div>
                                
                                <button v-if="!consultar" type="button" class="btn btn-primary pull-right" @click="formEvaluarCita">
                                    <i class="fa fa-search"> 
                                    </i>
                                    Consultar
                                </button>

                                <div class="form-group text-right" v-if="consultar&&disponibles">

                                    <button  type="button" class="btn btn-success" @click="reintentar">
                                        <i class="fa fa-calendar"> 
                                        </i>
                                        Cambiar Fecha
                                    </button>

                                    <button v-if="consultar&&disponibles" type="button" class="btn btn-primary" @click="formAddCita">
                                        <i class="fa fa-save"> 
                                        </i>
                                        Guardar
                                    </button>

                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                
                <div class="col-xs-12 col-md-6 col-lg-6 col-xl-6">
                    <div class="card">
                        <div class="card-body">

                            <div class ="form-group">
                                <label class="text-primary">Citas Planeadas</label>
                            </div>
                            
                            <vue-good-table 
                                :columns="columns" :rows="citas" 
                                theme="black-rhino" 
                                :sort-options="{
                                    enabled: true,
                                }"
                                :search-options="{
                                    enabled: true,
                                    placeholder: 'Buscar en la tabla',
                                }" 
                                :pagination-options="{
                                    enabled: true,
                                    mode: 'records',
                                    perPage: 5,
                                    perPageDropdown: [3, 7, 9],
                                    dropdownAllowAll: true,
                                    setCurrentPage: 1,
                                    nextLabel: 'siguiente',
                                    prevLabel: 'anterior',
                                    rowsPerPageLabel: 'Filas por página',
                                    ofLabel: 'de',
                                    pageLabel: 'página', // for 'pages' mode
                                    allLabel: 'Todos',
                                }">

                                    <div v-if="cargando" slot="emptystate">
                                        <div class="form-group text-center">
                                            <div class="fa-3x">
                                                <i class="fas fa-sync fa-spin"></i>
                                            </div>
                                                Cargando...
                                        </div>
                                    </div>

                                    <div v-else slot="emptystate">
                                        <div class="form-group text-center">
                                            Sin citas para mostrar
                                        </div>
                                    </div>
                                    
                                    <template slot="table-row" slot-scope="props">
                                        <span v-if="props.column.field == 'acciones'">
                                            <div class="form-group text-center">
                                                <button  class="btn btn-danger btn-sm" title="eliminar cita" @click="eliminarCita(props.row)">
                                                    <i class="fa fa-trash">
                                                    </i>    
                                                </button>
                                                <button  class="btn btn-info btn-sm" title="ver cita" @click="verCita(props.row)">
                                                    <i class="fa fa-eye">
                                                    </i>
                                                </button>
                                            </div>
                                        </span>
                                    </template> 

                            </vue-good-table>

                        </div>
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
    import 'animate.css';
    import Swal from 'sweetalert2';

    export default {
        mounted() { 
            
            this.listarCitas();

        },
        validations: {
            cita: {
                email: {
                    required,
                    email
                },
                startDate:{
                    required
                }
            }
        },
        data () {
            return {
                showCita: false,
                cargando: false,
                disponibles: false,
                consultar: false,
                horas:[],
                disabledDates: {
                    to: new Date(), //antes de esta fecha no se puede elegir
                    from: new Date(2021,1,1), // desabilitar hasta esta fecha
                    days: [6, 0], // descargar fin de semana
                },
                es: es,
                submitted: false,
                citas: [],
                cita:{
                    startDate: '',
                    email : '',
                    hourDate: ''
                },
                columns: [
                    {
                        label: 'Email',
                        field: 'email',
                    },
                    {
                        label: 'Hora',
                        field: 'hour_date',
                        type: 'number',
                    },
                    {
                        label: 'Fecha Cita',
                        field: 'start_date',
                        type: 'date',
                        dateInputFormat: 'yyyy-MM-dd',
                        dateOutputFormat: 'dd-MM-yyyy',
                    },
                    {
                        label: 'Creacion',
                        field: 'created_at',
                        type: 'date',
                        dateInputFormat: 'yyyy-MM-dd hh:mm:ss',
                        dateOutputFormat: 'dd-MM-yyyy hh:mm:ss',
                    },
                    {
                        label: 'Acciones',
                        field: 'acciones',
                        type: 'text',
                    }
                ],
             
            }
        },
        methods: {
            cerrarModal(){

                this.showCita = false;

            },
            verCita(cita){

                this.cita = {
                    startDate : cita.start_date,
                    hourDate: cita.hour_date,
                    email: cita.email,
                    creacion: cita.created_at
                };

                this.showCita = true;

            },
            //conserva el email
            reintentar(){

                this.submitted = false;
                this.cita.startDate = "";
                this.cita.hourDate = "";
                this.disponibles = false;
                this.consultar = false;
                this.horas = [];

            },
            //limpia todo el ciclo
            limpiarFormulario(){

                this.submitted = false;
                this.cita = {
                    startDate: "",
                    email: "",
                    hourDate: ""
                };
                this.disponibles = false;
                this.consultar = false;
                this.horas = [];

            },
            //lista las citas
            listarCitas(){
                
                this.cargando = true;
                axios.get(`/api/citas`)
                .then(response => {

                    this.citas = response.data;
                    this.cargando = false;
                })
                .catch(e => {

                    console.log("error: ",e);
                })          

            },
            //clase error o exito si es que se envio el formulario
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
            //consultar por
            formEvaluarCita(e) {
      
                this.submitted = true;

                axios.post(`/api/evaluar_cita`, {
                    start_date: moment(this.cita.startDate).format("YYYY-MM-DD"),
                    email: this.cita.email
                })
                .then(response => {
                    console.log("la response eval es: ", response);
                    //si ocurrio un error en backend manejado por mi
                    if(response.data.status == 0){

                        const errores = response.data.errores.email;
                        Swal.fire({
                            title: `${errores.map( e => e)}`,
                            icon: "error",
                            timer: 2500,
                            showConfirmButton: false
                        });
                        this.limpiarFormulario();

                    }else{

                        this.horas = response.data.horasDisponibles;

                        if(this.horas.length > 0){

                            this.disponibles = true;

                        }
                        this.consultar = true;
                    }
            

                })
                .catch(e => {
                    console.log("error evaluacion: ", e);
                    this.horas = []; 
                    this.consultar = false;
                    console.log("error: ",e);
                  
                });
                
            },
            formAddCita(e) {
      
                this.submitted = true;

                this.$v.$touch();
                if (this.$v.$invalid) {
                    console.log("formulario invalido");
                    return;
                }
         
                //console.log("el requerimiento antes de enviarlo es: ", formData);
                axios.post(`/api/cita`, {
                    start_date: moment(this.cita.startDate).format("YYYY-MM-DD"),
                    hour_date: this.cita.hourDate.hora,
                    email: this.cita.email
                })
                .then(response => {

                    console.log("respuesta guardar cita: ", response);
                    this.listarCitas();
                    this.limpiarFormulario();

                    Swal.fire({
                        title: 'Cita reservada!',
                        icon: "success",
                        timer: 2500,
                        showConfirmButton: false
                    });

                })
                .catch(e => {

                    console.log("error: ",e);
                  
                });
                
            },
            eliminarCita(data) {
                    
                const cita = data;
            
                Swal.fire({
                    title: `¿Esta seguro que desea elminar la cita del usuario ${cita.email}?`,
                    text: 'No podrá deshacer los cambios',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, eliminar!'
                }).then((result) => {
                    
                    if (result.value) {

                        axios.delete(`/api/cita/${cita.id}`)
                            .then(response => {
                                console.log("la respuesta al eliminar la cita es: ", response);
                                this.listarCitas();
                                this.limpiarFormulario();
                                Swal.fire({
                                    title: 'Cita eliminada',
                                    icon: "success",
                                    timer: 2500,
                                    showConfirmButton: false
                                });
                            })
                            .catch(e => {
                                //this.errors.push(e)
                            })
                    }
                });
            },
        },
        components:{
            Datepicker,
            VueGoodTable
        }
    }

</script>
