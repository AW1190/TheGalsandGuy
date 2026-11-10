import { Button, Paper, Table, TableBody, TableCell, TableHead, TableRow } from "@mui/material";
import type { DashboardData } from "./dashboard-data";
import { DashboardPanel } from "./dashboard-panel";
import { StatusBadge } from "./status-badge";

const money = new Intl.NumberFormat("en-GB", { style: "currency", currency: "GBP" });

function formatDate(value: string) {
  const date = new Date(value.replace(" ", "T"));
  return Number.isNaN(date.getTime())
    ? value
    : new Intl.DateTimeFormat("en-GB", { day: "2-digit", month: "short", year: "numeric" }).format(date);
}

export function RecentOrders({ orders }: { orders: DashboardData["recentOrders"] }) {
  return (
    <DashboardPanel
      title="Recent orders"
      subtitle="Latest orders across your business"
      action={<Button size="small" sx={{ fontSize: 11 }}>View all orders</Button>}
    >
      <Paper variant="outlined" sx={{ mt: 2, borderColor: "#e8edea", borderRadius: 1.5, overflowX: "auto" }}>
        <Table size="small" aria-label="Recent orders">
          <TableHead>
            <TableRow sx={{ bgcolor: "#f8faf9" }}>
              {["ORDER", "CUSTOMER", "DATE", "AMOUNT", "STATUS"].map((heading) => (
                <TableCell key={heading} sx={{ color: "#89968e", fontSize: 10, fontWeight: 600, whiteSpace: "nowrap" }}>
                  {heading}
                </TableCell>
              ))}
            </TableRow>
          </TableHead>
          <TableBody>
            {orders.map((order) => (
              <TableRow key={order.id} hover>
                <TableCell sx={{ color: "#287348", fontSize: 11, fontWeight: 600, whiteSpace: "nowrap" }}>ORD-{String(order.id).padStart(3, "0")}</TableCell>
                <TableCell sx={{ fontSize: 11, whiteSpace: "nowrap" }}>{order.customer}</TableCell>
                <TableCell sx={{ fontSize: 11, color: "text.secondary", whiteSpace: "nowrap" }}>{formatDate(order.date)}</TableCell>
                <TableCell sx={{ fontSize: 11, fontWeight: 600, whiteSpace: "nowrap" }}>{money.format(order.amount)}</TableCell>
                <TableCell><StatusBadge status={order.status.charAt(0).toUpperCase() + order.status.slice(1)} /></TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Paper>
    </DashboardPanel>
  );
}
