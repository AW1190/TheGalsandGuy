import { Box, ButtonBase, Stack, Typography } from "@mui/material";
import DashboardRoundedIcon from "@mui/icons-material/DashboardRounded";
import Inventory2OutlinedIcon from "@mui/icons-material/Inventory2Outlined";
import ShoppingBasketOutlinedIcon from "@mui/icons-material/ShoppingBasketOutlined";
import PeopleOutlineRoundedIcon from "@mui/icons-material/PeopleOutlineRounded";
import BarChartOutlinedIcon from "@mui/icons-material/BarChartOutlined";

const navigation = [
  { label: "Dashboard", icon: <DashboardRoundedIcon fontSize="small" /> },
  { label: "Products", icon: <Inventory2OutlinedIcon fontSize="small" /> },
  { label: "Orders", icon: <ShoppingBasketOutlinedIcon fontSize="small" />, count: "128" },
  { label: "Customers", icon: <PeopleOutlineRoundedIcon fontSize="small" /> },
  { label: "Reports", icon: <BarChartOutlinedIcon fontSize="small" /> },
];

export function Navigation({
  activePage,
  onNavigate,
}: {
  activePage: string;
  onNavigate: (page: string) => void;
}) {
  return (
    <Box>
      <Typography variant="overline" sx={{ px: 1, mb: 1, color: "#96a199", fontSize: 10, letterSpacing: 1 }}>
        MENU
      </Typography>
      <Stack component="nav" aria-label="Main navigation" sx={{ gap: 0.5 }}>
        {navigation.map((item) => {
          const selected = activePage === item.label;
          return (
            <ButtonBase
              key={item.label}
              onClick={() => onNavigate(item.label)}
              aria-current={selected ? "page" : undefined}
              sx={{
                minHeight: 38,
                px: 1,
                justifyContent: "flex-start",
                gap: 1.25,
                borderRadius: 1.5,
                color: selected ? "#236b43" : "#66746b",
                bgcolor: selected ? "#eaf4ed" : "transparent",
                "&:hover": { bgcolor: selected ? "#eaf4ed" : "#f5f8f6" },
              }}
            >
              {item.icon}
              <Typography sx={{ flex: 1, textAlign: "left", fontSize: 12, fontWeight: selected ? 600 : 400 }}>
                {item.label}
              </Typography>
              {item.count && <Typography variant="caption" sx={{ color: "#819087" }}>{item.count}</Typography>}
            </ButtonBase>
          );
        })}
      </Stack>
    </Box>
  );
}
