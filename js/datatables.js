$(document).ready(function () {
    $('.datatable').DataTable({
        paging: false,
        bInfo: false,
        searchPlaceholder: "Search records",
        "language": {
            sSearch: "",
            searchPlaceholder: "Suchen...",
            sLoadingRecords: "Wird geladen...",
            sProcessing: "Bitte warten...",
            sZeroRecords: "Keine Einträge vorhanden.",
        }
    });
});