<script setup>
import draggable from 'vuedraggable'
import Chip from "primevue/chip";
import Button from "primevue/button";

const props = defineProps(['children'])
const emit = defineEmits(['success', 'click-edit', 'click-delete', 'click-new', 'click-action-new', 'click-action-delete'])

const finish = () => {
    emit('success', props.children)
}

const clickModuleNew = (val) => {
    emit('click-new', val)
}

const clickModuleEdit = (val) => {
    emit('click-edit', val)
}

const clickModuleDelete = (val) => {
    emit('click-delete', val)
}

const clickActionNew = (val) => {
    emit('click-action-new', val)
}

const clickActionDelete = (val) => {
    emit('click-action-delete', val)
}

</script>

<template>
    <draggable class="dragArea"
               tag="ul"
               :list="children"
               :group="{ name: 'g1' }"
               item-key="ModuloId"
               handle=".handle"
               @end="finish">
        <template #item="{ element }">
            <li>
                <div style="display: flex; align-items: center" class="x-module-hover-icon x-div-module">
                    <i class="ti ti-arrows-move handle" style="cursor: pointer; font-size: 36px"></i>
                    <i :class="`ti ti-${element.Icono} icon`" style="font-size: 24px; padding: 0 16px"></i>
                    <div class="flex column q-ml-md" style="width: 100%; padding: 4px 0;">
                        <div>{{ element.Etiqueta }}</div>
                        <div>{{ element.Valor }}</div>
                        <div class="flex text-primary" style="font-size: 12px" v-if="element.Url">
                            <div v-if="element.Url">Link: {{ element.Url }}</div>
                        </div>
                        <div>
                            <Chip v-for="accion in element.acciones"
                                  style="margin-right: 8px; margin-top: 4px; margin-bottom: 4px; padding: 4px 8px 4px 16px; font-size: 13px;">
                                <span>{{ accion.Nombre }}</span>
                                <i class="ti ti-x" style="cursor: pointer" @click="clickActionDelete(accion)"></i>
                            </Chip>
                            <Chip style="margin-top: 4px; margin-bottom: 4px; cursor: pointer; background-color: #7367F0; color: #fff; padding: 4px 8px 4px 16px; font-size: 13px;"
                                  @click="clickActionNew(element)">
                                <span>Nueva acción</span>
                                <i class="ti ti-plus" style="cursor: pointer"></i>
                            </Chip>
                        </div>
                    </div>
                    <Button icon="ti ti-plus"
                            rounded
                            variant="outlined"
                            aria-label="Filter"
                            size="small"
                            style="margin-right: 8px;"
                            @click="$emit('click-new', element)" />

                    <Button icon="ti ti-pencil"
                            rounded
                            variant="outlined"
                            aria-label="Filter"
                            size="small"
                            severity="info"
                            style="margin-right: 8px;"
                            @click="$emit('click-edit', element)" />

                    <Button icon="ti ti-trash"
                            rounded
                            variant="outlined"
                            aria-label="Filter"
                            size="small"
                            severity="danger"
                            @click="$emit('click-delete', element)" />
                </div>
                <base-nested :children="element.children"
                             @success="finish"
                             @click-new="clickModuleNew"
                             @click-edit="clickModuleEdit"
                             @click-delete="clickModuleDelete"
                             @click-action-new="clickActionNew"
                             @click-action-delete="clickActionDelete"/>
            </li>
        </template>
    </draggable>
</template>
