<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-btn color="primary" @click="$emit('add')">Add New</v-btn>
    </v-card-title>
    <v-data-table
      :headers="headers"
      :items="items"
      :loading="loading"
      class="elevation-1"
    >
      <template v-slot:item.actions="{ item }">
        <v-icon small class="mr-2" @click="$emit('edit', item)">mdi-pencil</v-icon>
        <v-icon small @click="$emit('delete', item)">mdi-delete</v-icon>
      </template>
    </v-data-table>
  </v-card>
</template>

<script>
export default {
  name: 'DataTable',
  props: {
    headers: Array,
    items: Array,
    loading: Boolean
  }
}
</script>
