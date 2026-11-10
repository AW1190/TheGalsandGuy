import type { ReactNode } from "react";
import AttachMoneyRoundedIcon from "@mui/icons-material/AttachMoneyRounded";
import ShoppingBasketOutlinedIcon from "@mui/icons-material/ShoppingBasketOutlined";
import PeopleOutlineRoundedIcon from "@mui/icons-material/PeopleOutlineRounded";
import Inventory2OutlinedIcon from "@mui/icons-material/Inventory2Outlined";
import { Box } from "@mui/material";
import type { DashboardData, MetricIcon } from "./dashboard-data";
import { MetricCard } from "./metric-card";

const icons: Record<MetricIcon, ReactNode> = {
  revenue: <AttachMoneyRoundedIcon fontSize="small" />,
  orders: <ShoppingBasketOutlinedIcon fontSize="small" />,
  customers: <PeopleOutlineRoundedIcon fontSize="small" />,
  stock: <Inventory2OutlinedIcon fontSize="small" />,
};

const money = new Intl.NumberFormat("en-GB", {
  style: "currency",
  currency: "GBP",
  maximumFractionDigits: 0,
});

export function MetricGrid({
  metrics,
  lowStockThreshold,
}: {
  metrics: DashboardData["metrics"];
  lowStockThreshold: number;
}) {
  const cards = [
    { label: "Revenue", value: money.format(metrics.revenue), detail: "Active orders", icon: "revenue" as const, tone: "#287348" },
    { label: "Orders", value: String(metrics.orders), detail: "All orders", icon: "orders" as const, tone: "#5486a5" },
    { label: "Customers", value: String(metrics.customers), detail: "Registered customers", icon: "customers" as const, tone: "#8171aa" },
    { label: "Low stock", value: String(metrics.lowStock), detail: `Products below ${lowStockThreshold} units`, icon: "stock" as const, tone: "#ba8742" },
  ];

  return (
    <Box sx={{ display: "grid", gridTemplateColumns: { xs: "repeat(2, minmax(0, 1fr))", lg: "repeat(4, minmax(0, 1fr))" }, gap: 1.5 }}>
      {cards.map((metric) => (
        <MetricCard key={metric.label} {...metric} icon={icons[metric.icon]} />
      ))}
    </Box>
  );
}
