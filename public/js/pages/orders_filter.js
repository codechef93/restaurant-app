class Orders_filter {
  load_listing_table(filter_by) {
    "use strict";
    var listing_table = $("#listing-table").DataTable({
      destroy: true,

      ajax: {
        url: "/api/orders",
        type: "POST",
        data: {
          access_token: window.settings.access_token,
          filter_by: filter_by,
        },
      },

      columns: [
        { name: "orders.order_number" },
        { name: "orders.table_number" },
        { name: "orders.customer_name" },
        { name: "orders.customer_phone" },
        { name: "orders.total_order_amount" },
        { name: "master_status.label" },
        { name: "orders.created_at" },
        { name: "orders.updated_at" },
        { name: "user_created.fullname" },
        {
          name: "data",
          render: function (data, type) {
            if (type === "display") {
              return '<input type="checkbox">';
            }

            return data;
          },
        },
        { name: "orders.order_action" },
      ],
      order: [[1, "asc"]],
      columnDefs: [
        { orderable: false, targets: [9] },
        {
          targets: [4],
          className: "text-right",
        },
      ],
    });
  }
}
