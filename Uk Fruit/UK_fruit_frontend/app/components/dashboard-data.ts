export type MetricIcon = "revenue" | "orders" | "customers" | "stock";

export type DashboardData = {
  metrics: {
    revenue: number;
    orders: number;
    customers: number;
    lowStock: number;
  };
  orderActivity: { status: string; count: number }[];
  monthlyRevenue: { month: string; revenue: number }[];
  recentOrders: {
    id: number;
    customer: string;
    date: string;
    amount: number;
    status: string;
  }[];
  lowStockThreshold: number;
};

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === "object" && value !== null;
}

export function isDashboardData(value: unknown): value is DashboardData {
  if (!isRecord(value) || !isRecord(value.metrics)) return false;
  const metrics = value.metrics;
  const validActivity = Array.isArray(value.orderActivity) && value.orderActivity.every(
    (item) => isRecord(item) && typeof item.status === "string" && typeof item.count === "number",
  );
  const validRevenue = Array.isArray(value.monthlyRevenue) && value.monthlyRevenue.every(
    (item) => isRecord(item) && typeof item.month === "string" && typeof item.revenue === "number",
  );
  const validOrders = Array.isArray(value.recentOrders) && value.recentOrders.every(
    (item) =>
      isRecord(item) &&
      typeof item.id === "number" &&
      typeof item.customer === "string" &&
      typeof item.date === "string" &&
      typeof item.amount === "number" &&
      typeof item.status === "string",
  );

  return (
    typeof metrics.revenue === "number" &&
    typeof metrics.orders === "number" &&
    typeof metrics.customers === "number" &&
    typeof metrics.lowStock === "number" &&
    validActivity &&
    validRevenue &&
    validOrders &&
    typeof value.lowStockThreshold === "number"
  );
}
