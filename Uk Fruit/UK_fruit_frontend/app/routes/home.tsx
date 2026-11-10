import { useEffect, useState } from "react";
import type { Route } from "./+types/home";
import {
  Avatar,
  Box,
  Button,
  CircularProgress,
  CssBaseline,
  Alert,
  Stack,
  ThemeProvider,
  Typography,
  createTheme,
} from "@mui/material";
import AddRoundedIcon from "@mui/icons-material/AddRounded";
import { MetricGrid } from "../components/metric-grid";
import { isDashboardData, type DashboardData } from "../components/dashboard-data";
import { OrderActivity } from "../components/order-activity";
import { RecentOrders } from "../components/recent-orders";
import { RevenueOverview } from "../components/revenue-overview";
import { Sidebar } from "../components/sidebar";

const theme = createTheme({
  palette: {
    mode: "light",
    primary: { main: "#287348" },
    background: { default: "#f7f9f8", paper: "#ffffff" },
  },
  typography: {
    fontFamily: '"Inter", ui-sans-serif, system-ui, sans-serif',
    button: { textTransform: "none", fontWeight: 600 },
  },
  shape: { borderRadius: 8 },
});

export function meta({}: Route.MetaArgs) {
  return [
    { title: "Dashboard | UK Fruit" },
    { name: "description", content: "Monitor your UK Fruit stock, orders and customers." },
  ];
}

export default function Home() {
  const [activePage, setActivePage] = useState("Dashboard");
  const [dashboard, setDashboard] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let active = true;

    async function loadDashboard() {
      setLoading(true);
      setError(null);
      try {
        const apiUrl = import.meta.env.VITE_PHP_API_URL ?? "http://127.0.0.1:8000";
        const response = await fetch(`${apiUrl.replace(/\/$/, "")}/dashboard.php`);
        const result: unknown = await response.json();

        if (!response.ok) {
          const message =
            typeof result === "object" && result !== null && "error" in result &&
            typeof result.error === "string"
              ? result.error
              : `Dashboard API returned HTTP ${response.status}.`;
          throw new Error(message);
        }
        if (!isDashboardData(result)) {
          throw new Error("Dashboard API returned data in an unexpected format.");
        }
        if (active) setDashboard(result);
      } catch (loadError) {
        if (active) {
          setError(loadError instanceof Error ? loadError.message : "Could not load dashboard data.");
        }
      } finally {
        if (active) setLoading(false);
      }
    }

    void loadDashboard();
    return () => {
      active = false;
    };
  }, []);

  return (
    <ThemeProvider theme={theme}>
      <CssBaseline />
      <Box sx={{ minHeight: "100vh", display: "flex", bgcolor: "background.default" }}>
        <Sidebar activePage={activePage} onNavigate={setActivePage} />
        <Box component="main" sx={{ flex: 1, minWidth: 0 }}>
          <Box
            component="header"
            sx={{
              height: 58,
              px: { xs: 2, md: 3 },
              display: "flex",
              alignItems: "center",
              justifyContent: "space-between",
              bgcolor: "background.paper",
              borderBottom: "1px solid #e8edea",
            }}
          >
            <Typography variant="caption" color="text.secondary">
              Workspace <Box component="span" sx={{ px: 1, color: "#bdc6c0" }}>/</Box>
              <Box component="span" sx={{ color: "#405047" }}>{activePage}</Box>
            </Typography>
            <Stack sx={{ flexDirection: "row", alignItems: "center", gap: 1 }}>
              <Typography variant="caption" color="text.secondary">UK Fruit Ltd</Typography>
              <Avatar sx={{ width: 28, height: 28, bgcolor: "#e9f1eb", color: "#41664e", fontSize: 11 }}>JD</Avatar>
            </Stack>
          </Box>

          <Stack sx={{ maxWidth: 1440, mx: "auto", px: { xs: 2, md: 3 }, py: 3, gap: 2 }}>
            <Stack sx={{ flexDirection: "row", justifyContent: "space-between", alignItems: "flex-end", gap: 2 }}>
              <Box>
                <Typography variant="caption" sx={{ color: "#98a39c", letterSpacing: 0.8 }}>
                  TUESDAY, 10 NOVEMBER 2026
                </Typography>
                <Typography variant="h5" sx={{ mt: 0.5, fontSize: 22, fontWeight: 650, color: "#28372d" }}>
                  {activePage}
                </Typography>
                <Typography variant="body2" color="text.secondary" sx={{ mt: 0.5, fontSize: 12 }}>
                  Here’s what’s happening with your business today.
                </Typography>
              </Box>
              <Button variant="contained" size="small" startIcon={<AddRoundedIcon />}>
                Create order
              </Button>
            </Stack>

            {loading && (
              <Stack sx={{ alignItems: "center", py: 8 }} aria-label="Loading dashboard data">
                <CircularProgress size={28} />
              </Stack>
            )}
            {error && <Alert severity="error">{error}</Alert>}
            {dashboard && (
              <>
                <MetricGrid metrics={dashboard.metrics} lowStockThreshold={dashboard.lowStockThreshold} />
                <Box sx={{ display: "grid", gridTemplateColumns: { xs: "1fr", md: "minmax(0, 1.7fr) minmax(260px, 1fr)" }, gap: 1.5 }}>
                  <RevenueOverview revenue={dashboard.monthlyRevenue} total={dashboard.metrics.revenue} />
                  <OrderActivity activity={dashboard.orderActivity} />
                </Box>
                <RecentOrders orders={dashboard.recentOrders} />
              </>
            )}
          </Stack>
        </Box>
      </Box>
    </ThemeProvider>
  );
}
