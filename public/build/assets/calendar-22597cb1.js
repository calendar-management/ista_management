$(document).ready(function(){class d{constructor(){this.date=new Date,this.hasUnsavedChanges=!1,this.modules=this.fetchModules(),this.holidays=this.fetchHolidays(),this.$calendar=$("#calendar"),this.$moduleSelect=$("#moduleSelect"),this.$weekSelect=$("#weekSelect"),this.$hoursCompleted=$("#hoursCompleted"),this.$saveBtn=$("#saveAllChangesBtn"),this.$updateStatus=$("#updateStatus"),this.init()}init(){this.addCustomStyles(),this.initCalendar(),this.createAddEventDialog(),this.createSaveButton(),this.createWeeklyUpdateForm(),this.updateAllModulesProgress(),this.setupUnsavedChangesWarning(),this.updateCalendar(),this.setUnsavedChanges(!1)}fetchModules(){return typeof data<"u"?data:(console.warn("Module data not found. Using empty array."),[])}fetchHolidays(){return holidays}formatDate(t,e="db"){const s=new Date(t);switch(s.setHours(12,0,0,0),e){case"db":const a=s.getFullYear(),r=String(s.getMonth()+1).padStart(2,"0"),o=String(s.getDate()).padStart(2,"0");return`${a}-${r}-${o}`;case"display":return s.toLocaleDateString(void 0,{weekday:"long",year:"numeric",month:"long",day:"numeric"});case"short":const n=String(s.getMonth()+1).padStart(2,"0"),l=String(s.getDate()).padStart(2,"0");return`${["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"][s.getDay()]} ${l}/${n}`}}prepareModulesForDatabase(){return this.modules.map(t=>{const e={moduleId:t.id,moduleName:t.name,startDate:this.formatDate(t.startDate),examDate:t.examDate?this.formatDate(new Date(t.examDate)):null,completedHours:t.completedHours,weeklyProgress:t.weeklyProgress,totalHours:t.totalHours,weeklyHours:t.weeklyHours,remainingHours:t.totalHours-t.completedHours,customSessionDates:t.customSessionDates};if(t.examDate){const a=Math.ceil(t.totalHours/t.weeklyHours);for(e.customSessionDates||(e.customSessionDates=[]);e.customSessionDates.length<=a;)e.customSessionDates.push(null);e.customSessionDates[a]=t.examDate,e.finalExamDate=t.examDate}return e})}updateWeeklyProgress(t,e,s){const a=this.modules.findIndex(o=>o.id===t);if(a===-1)return!1;const r=this.modules[a];for(;r.weeklyProgress.length<=e;)r.weeklyProgress.push(null);return r.weeklyProgress[e]=s,r.completedHours=r.weeklyProgress.filter(o=>o!==null).reduce((o,n)=>o+n,0),this.refreshUI(t),this.setUnsavedChanges(!0),{module:r}}refreshUI(t=null){this.updateCalendar(),t&&this.updateProgressDisplay(t),this.updateAllModulesProgress()}updateProgressDisplay(t){const e=this.modules.find(a=>a.id===t);if(!e)return;const s=(e.completedHours/e.totalHours*100).toFixed(1);$("#progressDisplayContainer").html(`
                <h4>${e.name} Progress</h4>
                <div class="progress mt-2" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="width: ${s}%;"
                        aria-valuenow="${e.completedHours}"
                        aria-valuemin="0"
                        aria-valuemax="${e.totalHours}">
                        ${e.completedHours}/${e.totalHours} hours (${s}%)
                    </div>
                </div>
                <div class="mt-3">
                    <strong>Date de début :</strong> ${this.formatDate(new Date(e.startDate),"display")}<br>
                    ${e.examDate?`<strong>Date d'examen :</strong> ${this.formatDate(new Date(e.examDate),"display")}`:""}
                </div>
            `)}updateAllModulesProgress(){let t=`
                    <div class="card mt-4 mb-4">
                        <div class="card-header">
                            <h4>Progression de tous les modules</h4>
                        </div>
                        <div class="card-body">
                `;this.modules.forEach(e=>{const s=Math.min(e.completedHours,e.totalHours),a=Math.max(e.totalHours-s,0),r=Math.min((s/e.totalHours*100).toFixed(1),100);t+=`
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <h5>${e.name}</h5>
                                <span>${s}/${e.totalHours} heures (${a} restantes)</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: ${r}%;"
                                    aria-valuenow="${s}"
                                    aria-valuemin="0"
                                    aria-valuemax="${e.totalHours}">
                                    ${r}%
                                </div>
                            </div>
                        </div>
                    `}),t+=`
                        </div>
                    </div>
                `,$("#allModulesProgressContainer").length===0&&$('<div id="allModulesProgressContainer"></div>').insertAfter("#weeklyUpdateContainer"),$("#allModulesProgressContainer").html(t)}getWeekDates(t,e){const s=this.modules.find(n=>n.id===t);if(!s)return[];const a=new Date(s.startDate);a.setHours(12,0,0,0),a.getDay();const r=[];let o=new Date(a);for(let n=0;n<e;n++)if(s.customSessionDates&&s.customSessionDates[n])r.push(new Date(s.customSessionDates[n]));else{if(n===0){r.push(new Date(o));continue}if(o=new Date(r[n-1]),o.setDate(o.getDate()+7),this.isHolidayDate(o))for(;this.isHolidayDate(o);)o.setDate(o.getDate()+7);r.push(new Date(o))}return r}createWeeklyUpdateForm(){$("#weeklyUpdateContainer").html(`
                <div class="weekly-update-form p-3 border rounded">
                    <h4>Mettre à jour les heures hebdomadaires</h4>
                    <div class="form-group">
                        <label for="moduleSelect">Sélectionner un module :</label>
                        <select id="moduleSelect" class="form-control">
                            ${this.modules.map(t=>`<option value="${t.id}">${t.name}</option>`).join("")}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="weekSelect">Numéro de semaine :</label>
                        <select id="weekSelect" class="form-control">
                            <!-- Will be dynamically populated -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="hoursCompleted">Heures effectuées :</label>
                        <input type="number" id="hoursCompleted" class="form-control" min="0" max="40" step="0.5">
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <button id="updateProgressBtn" class="btn btn-primary btn-block">Enregistrer les heures</button>
                        </div>
                        <div class="col">
                            <button id="markAbsentBtn" class="btn btn-warning btn-block">Marquer comme 0 (Absent)</button>
                        </div>
                    </div>
                    <div id="updateStatus" class="mt-2"></div>
                </div>
            `),this.setupWeeklyFormEvents()}setupWeeklyFormEvents(){const t=this;$("#moduleSelect").on("change",function(){const e=parseInt($(this).val()),s=t.modules.find(a=>a.id===e);s&&(t.updateWeekSelectOptions(e),$("#hoursCompleted").val(s.weeklyHours),t.updateProgressDisplay(e))}),$("#weekSelect").on("change",function(){const e=parseInt($("#moduleSelect").val()),s=parseInt($(this).val()),a=t.modules.find(r=>r.id===e);a&&s<a.weeklyProgress.length&&a.weeklyProgress[s]!==null?$("#hoursCompleted").val(a.weeklyProgress[s]):a&&$("#hoursCompleted").val(a.weeklyHours)}),$("#updateProgressBtn").on("click",function(){const e=parseInt($("#moduleSelect").val()),s=parseInt($("#weekSelect").val()),a=parseFloat($("#hoursCompleted").val());t.updateWeeklyProgress(e,s,a)?$("#updateStatus").html(`
                        <div class="alert alert-success">
                            <strong>Succès !</strong> La semaine ${s+1} a été mise à jour avec ${a} heures.
                            <br><small>Souvenez-vous de cliquer sur "Enregistrer toutes les modifications" pour sauvegarder.</small>
                        </div>
                    `):$("#updateStatus").html('<div class="alert alert-danger">Échec de la mise à jour de la progression</div>')}),$("#markAbsentBtn").on("click",function(){$("#hoursCompleted").val(0),$("#updateProgressBtn").click()}),$("#moduleSelect").trigger("change")}updateWeekSelectOptions(t){const e=this.modules.find(o=>o.id===t);if(!e)return;const s=Math.ceil(e.totalHours/e.weeklyHours),r=this.getWeekDates(t,s).map((o,n)=>`<option value="${n}">Week ${n+1} - ${this.formatDate(o,"short")}</option>`).join("");$("#weekSelect").html(r)}addCustomStyles(){$("#calendarCustomStyles").length===0&&$('<style id="calendarCustomStyles">').text(`
                        #saveChangesCard {
                            position: sticky;
                            bottom: 20px;
                            z-index: 100;
                            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                            display: flex;
                            flex-diraction: column;
                        }
                        .save-notification { margin-bottom: 10px; }
                        .fc-event.module-start {color: #000000; background-color: #ffff; border-color: #007bff; }
                        .fc-event.module-exam {color: #000000; background-color: #dc3545; border-color: #dc3545; }
                        .fc-event.progress {color: #000000; background-color: #28a745; border-color: #28a745; }
                        .fc-event.absence {color: #000000; background-color: #ffc107; border-color: #ffc107; }
                        .fc-event.planned-session {color: #000000; background-color: #FFFF; border-color: #6c757d; opacity: 0.7; }
                        .fc-event {cursor: pointer;}
                        .fc-event.holiday-event {opacity: 0.7; z-index: 1;  color:rgb(239, 222, 222); background-color:rgb(12, 3, 4);}
                        .holiday-label {font-weight: bold; font-style: italic; z-index: 2; color:rgb(239, 222, 222);}
                    `).appendTo("head")}isHolidayDate(t){const e=this.formatDate(t);return this.holidays.some(s=>{const a=new Date(s.startDate),r=s.endDate?new Date(s.endDate):new Date(s.startDate),o=new Date(e);return a.setHours(0,0,0,0),r.setHours(23,59,59,999),o.setHours(12,0,0,0),o>=a&&o<=r})}updateCalendar(){this.$calendar.fullCalendar("removeEvents");const t=this.generateEvents(),e=this.generateHolidayEvents();this.$calendar.fullCalendar("addEventSource",t),this.$calendar.fullCalendar("addEventSource",e)}generateEvents(){const t=[];return this.modules.forEach(e=>{const s=new Date(e.startDate);s.setHours(12,0,0,0),t.push({id:"start_"+e.id,title:`${e.name} - Starts`,start:s,allDay:!0,className:"module-start",editable:!0,moduleId:e.id,type:"module-start"}),e.examDate&&t.push({id:"exam_"+e.id,title:e.name+" - Exam",start:new Date(e.examDate),allDay:!0,className:"module-exam",editable:!0,moduleId:e.id,type:"module-exam"});const a=Math.ceil(e.totalHours/e.weeklyHours),r=this.getWeekDates(e.id,a);for(let o=0;o<a;o++){let n=o<e.weeklyProgress.length&&e.weeklyProgress[o]!==null?e.weeklyProgress[o]:e.weeklyHours,l="planned-session",i="#6c757d";o<e.weeklyProgress.length&&e.weeklyProgress[o]!==null&&(e.weeklyProgress[o]>0?(l="progress",i="#28a745"):e.weeklyProgress[o]===0&&(l="absence",i="#ffc107")),t.push({id:"week_"+e.id+"_"+o,title:`${e.name} - Week ${o+1}: ${n} hrs`,start:r[o],allDay:!0,className:l,color:i,moduleId:e.id,weekIndex:o,type:"week",editable:!0})}}),t}generateHolidayEvents(){return this.holidays.map(t=>{let e,s,a,r;switch(t.type){case"vacance":e="rgba(255, 244, 245, 0.7)",s="#ff8a93",a="#dd2c41",r=`🏖️ ${t.name}`;break;case"stage":e="rgba(232, 245, 233, 0.7)",s="#81c784",a="#2e7d32",r=`🏢 Stage: ${t.name}`,t.affectedGroups&&t.affectedGroups.length>0&&(r+=` (${t.affectedGroups.join(", ")})`);break;case"regional":e="rgba(227, 242, 253, 0.7)",s="#64b5f6",a="#1565c0",r=`📝 ${t.name}`,t.additionalInfo&&(r+=` (${t.additionalInfo})`);break;default:e="rgba(255, 244, 245, 0.7)",s="#ff8a93",a="#000",r=t.name}const o=new Date(t.endDate);return o.setDate(o.getDate()+1),{id:"holiday_"+t.id,title:r,start:t.startDate,end:o.toISOString().split("T")[0],allDay:!0,backgroundColor:e,borderColor:s,textColor:a,className:`holiday-event holiday-${t.type}`,editable:!1,type:"holiday",extendedProps:{holidayType:t.type,additionalInfo:t.additionalInfo,affectedGroups:t.affectedGroups}}})}saveToDatabase(){const t=this.prepareModulesForDatabase();this.$saveBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'),this.$saveBtn.prop("disabled",!0);const e=JSON.stringify(t);$("#moduleDataInput").val(e),$.ajax({url:"/save-calendar-data",type:"POST",data:{moduleData:JSON.stringify(t),_token:$('meta[name="csrf-token"]').attr("content")},success:s=>{this.showSaveSuccess(),this.setUnsavedChanges(!1),this.$saveBtn.html('<i class="fas fa-save mr-1"></i> Save All Changes'),this.$saveBtn.prop("disabled",!0)},error:(s,a,r)=>{console.error("Save error:",r),console.error("Response:",s.responseText),this.showSaveError(r),this.$saveBtn.html('<i class="fas fa-save mr-1"></i> Save All Changes'),this.$saveBtn.prop("disabled",!1)}})}showSaveSuccess(){const t=$(`
                <div class="alert alert-success save-notification" role="alert">
                    <strong>Succès !</strong>Modifications enregistrées.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);$("#saveNotificationArea").html(t),setTimeout(()=>t.alert("close"),3e3)}showSaveError(t){const e=$(`
                <div class="alert alert-danger save-notification" role="alert">
                    <strong>Erreur !</strong> Échec de l'enregistrement les modifications. Veuillez réessayer.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);$("#saveNotificationArea").html(e)}setUnsavedChanges(t){this.hasUnsavedChanges=t,this.hasUnsavedChanges?(this.$saveBtn.removeClass("btn-outline-primary").addClass("btn-primary").prop("disabled",!1),$("#unsavedChangesAlert").length===0&&$("#saveNotificationArea").append(`
                        <div class="alert alert-warning" id="unsavedChangesAlert" role="alert">
                            <strong>Modifications non enregistrées !</strong> 
                        </div>
                    `)):(this.$saveBtn.removeClass("btn-primary").addClass("btn-outline-primary").prop("disabled",!0),$("#unsavedChangesAlert").remove())}updateModuleDate(t,e,s){if(this.isHolidayDate(s))return alert("Vous ne pouvez pas planifier d'événements pendant les périodes de congé."),!1;const a=this.modules.findIndex(o=>o.id===t);if(a===-1)return!1;if(e==="module-start"){const o=this.modules[a].weeklyHours;return this.updateModuleStartDate(t,s,o)}else if(e==="module-exam"){if(!this.validateExamDate(t))return!1;this.modules[a].examDate=this.formatDate(s)}return this.refreshUI(t),parseInt($("#moduleSelect").val())===t&&$("#moduleSelect").trigger("change"),this.setUnsavedChanges(!0),!0}deleteExamDate(t){const e=this.modules.findIndex(a=>a.id===t);return e===-1?!1:(this.modules[e].examDate=null,this.refreshUI(t),parseInt($("#moduleSelect").val())===t&&$("#moduleSelect").trigger("change"),this.setUnsavedChanges(!0),!0)}validateExamDate(t){const e=this.modules.find(a=>a.id===t);if(!e)return!1;const s=e.completedHours/e.totalHours*100;return s<95?(alert(`Vous ne pouvez pas programmer un examen avant d'avoir terminé au moins 95% du module. Progression actuelle: ${s.toFixed(1)}%`),!1):!0}updateProgressSessionDate(t,e,s){if(this.isHolidayDate(s))return alert("You cannot schedule events during holiday periods."),!1;const a=this.modules.findIndex(r=>r.id===t);if(a===-1)return!1;if(s.getDay()===0)return alert("Progress sessions cannot be scheduled on Sundays. Please choose another day."),!1;for(this.modules[a].customSessionDates||(this.modules[a].customSessionDates=[]);this.modules[a].customSessionDates.length<=e;)this.modules[a].customSessionDates.push(null);return this.modules[a].customSessionDates[e]=this.formatDate(s),this.updateCalendar(),parseInt($("#moduleSelect").val())===t&&this.updateWeekSelectOptions(t),this.setUnsavedChanges(!0),!0}createAddEventDialog(){const t=this;$("#addEventModal").length===0&&($("body").append(`
                    <div id="addEventModal" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Ajouter un événement</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="eventTypeSelect">Type d'événement :</label>
                                        <select id="eventTypeSelect" class="form-control">
                                            <option value="module-start">Début du module</option>
                                            <option value="module-exam">Examen du module</option>
                                            <option value="session">Session</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="eventModuleSelect">Module:</label>
                                        <select id="eventModuleSelect" class="form-control">
                                            ${this.modules.map(e=>`<option value="${e.id}">${e.name}</option>`).join("")}
                                        </select>
                                    </div>
                                    <div class="form-group" id="weeklyHoursGroup" style="display: none;">
                                        <label for="weeklyHoursInput">Heures hebdomadaires :</label>
                                        <input type="number" id="weeklyHoursInput" class="form-control" min="1" max="40" step="0.5">
                                    </div>
                                    <div class="form-group" id="weekNumberGroup" style="display: none;">
                                        <label for="eventWeekSelect">Numéro de semaine :</label>
                                        <select id="eventWeekSelect" class="form-control">
                                            <!-- Will be populated dynamically -->
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="eventDate">Date:</label>
                                        <input type="text" id="eventDate" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                    <button type="button" class="btn btn-primary" id="saveEventBtn">Enregistrer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `),$("#eventTypeSelect").on("change",function(){const e=$(this).val();if(e==="module-start"){$("#weeklyHoursGroup").show();const s=parseInt($("#eventModuleSelect").val()),a=t.modules.find(r=>r.id===s);a&&$("#weeklyHoursInput").val(a.weeklyHours)}else $("#weeklyHoursGroup").hide();e==="session"?($("#weekNumberGroup").show(),t.updateEventWeekOptions()):$("#weekNumberGroup").hide()}),$("#eventModuleSelect").on("change",function(){const e=$("#eventTypeSelect").val();if(e==="module-start"){const s=parseInt($(this).val()),a=t.modules.find(r=>r.id===s);a&&$("#weeklyHoursInput").val(a.weeklyHours)}e==="session"&&t.updateEventWeekOptions()}),$("#saveEventBtn").on("click",function(){const e=parseInt($("#eventModuleSelect").val()),s=$("#eventTypeSelect").val(),a=new Date($("#eventDate").val());let r=!1;if(s==="module-start"){const o=parseFloat($("#weeklyHoursInput").val());r=t.updateModuleStartDate(e,a,o)}else if(s==="session"){const o=parseInt($("#eventWeekSelect").val());r=t.updateProgressSessionDate(e,o,a)}else if(s==="module-exam"){const o=t.modules.find(n=>n.id===e);if(o){const n=o.completedHours/o.totalHours*100;if(n<95){alert(`Vous ne pouvez pas programmer un examen avant d'avoir terminé au moins 95% du module. Progression actuelle: ${n.toFixed(1)}%`);return}}r=t.updateModuleDate(e,s,a)}r&&($("#addEventModal").modal("hide"),t.setUnsavedChanges(!0))}))}updateModuleStartDate(t,e,s){if(this.isHolidayDate(e))return alert("You cannot schedule events during holiday periods."),!1;const a=this.modules.findIndex(o=>o.id===t);return a===-1?!1:(this.modules[a].startDate=this.formatDate(e),!isNaN(s)&&s>0&&(this.modules[a].weeklyHours=s),this.modules[a].customSessionDates=[],this.refreshUI(t),parseInt($("#moduleSelect").val())===t&&$("#moduleSelect").trigger("change"),this.setUnsavedChanges(!0),!0)}updateEventWeekOptions(){const t=parseInt($("#eventModuleSelect").val()),e=this.modules.find(o=>o.id===t);if(!e)return;const s=Math.ceil(e.totalHours/e.weeklyHours),r=this.getWeekDates(t,s).map((o,n)=>`<option value="${n}">Week ${n+1} - ${this.formatDate(o,"short")}</option>`).join("");$("#eventWeekSelect").html(r)}createSaveButton(){const t=this;$(`
                <div class="mt-4 card mb-4" id="saveChangesCard">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center">
                            <div id="saveNotificationArea"></div>
                            <button id="saveAllChangesBtn" class="btn btn-outline-primary" disabled>
                                <i class="fas fa-save mr-1"></i> Enregistrer toutes les modifications
                            </button>
                            
                        </div>
                    </div>
                </div>
            `).insertAfter("#calendar"),this.$saveBtn=$("#saveAllChangesBtn"),this.$saveBtn.on("click",function(){t.hasUnsavedChanges&&($(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'),$(this).prop("disabled",!0),t.saveToDatabase())})}setupUnsavedChangesWarning(){const t=this;$(window).on("beforeunload",function(){if(t.hasUnsavedChanges)return"Vous avez des modifications non enregistrées. Êtes-vous sûr de vouloir quitter sans enregistrer ?"})}initCalendar(){const t=this;this.$calendar.fullCalendar({header:{left:"prev,next today",center:"title",right:"month"},defaultView:"month",editable:!0,selectable:!0,selectHelper:!0,firstDay:1,hiddenDays:[0],select:function(e,s){if(t.isHolidayDate(e)){alert("Vous ne pouvez pas planifier d'événements pendant les périodes de congé.");return}else $("#eventDate").val(t.formatDate(e)),$("#eventTypeSelect").val("module-start").trigger("change"),$("#addEventModal").modal("show")},eventDrop:function(e,s,a){if(e.type==="holiday"){a();return}if(t.isHolidayDate(e.start)){alert("You cannot schedule events during holiday periods."),a();return}let o=!1;e.type==="module-start"?o=t.updateModuleStartDate(e.moduleId,e.start,t.modules.find(n=>n.id===e.moduleId).weeklyHours):e.type==="module-exam"?o=t.updateModuleDate(e.moduleId,e.type,e.start):e.type==="week"&&(o=t.updateProgressSessionDate(e.moduleId,e.weekIndex,e.start)),o||a()},eventClick:function(e,s,a){var r;e.type==="week"?($("#moduleSelect").val(e.moduleId).trigger("change"),$("#weekSelect").val(e.weekIndex).trigger("change"),$("html, body").animate({scrollTop:$("#weeklyUpdateContainer").offset().top-50},200),$("#hoursCompleted").focus().select()):e.type==="module-exam"&&((r=t.modules.find(o=>o.id===e.moduleId))!=null&&r.name,confirm("Êtes-vous sûr de vouloir supprimer la date d'examen pour ${moduleName} ?")&&t.deleteExamDate(e.moduleId)&&t.setUnsavedChanges(!0))},events:[]})}}new d});
