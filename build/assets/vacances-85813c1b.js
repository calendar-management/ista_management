$(document).ready(function(){var o=[],i=[],r=[];function l(){$.ajax({url:"/fetch-vacations",method:"GET",success:function(n){i=n.groupes.map(function(e){return{name:e.name,id:e.id_group}}),r=n.filieres.map(function(e){return{name:e.name,id:e.id_fillier}}),o=n.vacations.map(function(e){let t="";if(e.id_group){let a=i.find(d=>d.id===e.id_group);t=a?a.name:""}else if(e.id_fillier){let a=r.find(d=>d.id===e.id_fillier);t=a?a.name:""}return{id:e.id,type:e.type,startDate:e.date_debut,endDate:e.date_fin||e.date_debut,description:e.description_vacance,extraInfo:t}}),f()},error:function(n){console.error("Error fetching holidays:",n)}})}function c(n){var e="";n==="stage"?e=`
                <div class="form-group">
                    <label>Select Groups:</label>
                    <div class="checkbox-container">
                        ${i.map(t=>`
                            <div class="form-check">
                                <input type="checkbox" id="group_${t.id}" name="groupSelect[]" value="${t.id}" class="form-check-input">
                                <label class="form-check-label" for="group_${t.id}">${t.name}</label>
                            </div>
                        `).join("")}
                    </div>
                </div>
            `:n==="regional"&&(e=`
                <label for="filiereSelect">Select Filiere:</label>
                <select id="filiereSelect" name="filiereSelect" class="form-control">
                    ${r.map(t=>`<option value="${t.id}">${t.name}</option>`).join("")}
                </select>
            `),$("#dynamicField").html(e)}function f(){$("#calendar").fullCalendar("removeEvents");var n=o.map(e=>{let t=new Date(e.endDate);return t.setDate(t.getDate()),{id:e.id,title:`${e.type.toUpperCase()}: ${e.description} ${e.extraInfo?"("+e.extraInfo+")":""}`,start:e.startDate,end:e.startDate===e.endDate?null:t,allDay:!0,color:e.type==="vacance"?"#ff0000":e.type==="stage"?"#007bff":"#28a745",editable:!1}});$("#calendar").fullCalendar("addEventSource",n)}$("#eventType").on("change",function(){c($(this).val())}),$("#holidayFormContainer").on("submit",function(){setTimeout(l,1e3)}),$("#calendar").fullCalendar({header:{left:"prev,next today",center:"title",right:"month,listYear"},defaultView:"month",editable:!1,selectable:!1,firstDay:1,hiddenDays:[0],events:[],eventClick:function(n){confirm(`Do you want to delete this event: ${n.title}?`)&&$.ajax({url:"/delete-vacation/"+n.id,method:"DELETE",data:{_token:$('meta[name="csrf-token"]').attr("content")},success:function(){l()},error:function(e){console.error("Error deleting event:",e)}})}}),l()});
